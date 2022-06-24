<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Exceptions;

use BeyondCode\DumpServer\RequestContextProvider;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Exception;
use Spatie\FlareClient\Context\BaseContextProviderDetector;
use Spatie\FlareClient\Context\ConsoleContextProvider;
use Spatie\FlareClient\Truncation\ReportTrimmer;
use Spatie\LaravelIgnition\ContextProviders\LaravelContextProviderDetector;
use Spatie\LaravelIgnition\Facades\Flare;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;
use Spatie\FlareClient\Report;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(
            function (Throwable $e) {
                //
            }
        );
    }


    /**
     * Converts the Exception in a PHP Exception to be able to serialize it.
     *
     * @param \Exception $exception
     * @return \Symfony\Component\Debug\Exception\FlattenException
     * @source https://github.com/squareboat/sneaker/blob/master/src/ExceptionHandler.php
     */
    private function getFlattenedException($exception)
    {
        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::createFromThrowable($exception);
        }

        return $exception;
    }

    public function report(Throwable $e)
    {
        parent::report($e);

        $flat = $this->getFlattenedException($e);
        $flare = Flare::make()
            ->setStage(app()->environment())
            ->setContextProviderDetector(new LaravelContextProviderDetector())
            ->setApiToken('')
            ->registerMiddleware(collect(config('flare.flare_middleware'))
                                     ->map(function ($value, $key) {
                                         if (is_string($key)) {
                                             $middlewareClass = $key;
                                             $parameters = $value ?? [];
                                         } else {
                                             $middlewareClass = $value;
                                             $parameters = [];
                                         }

                                         return new $middlewareClass(...array_values($parameters));
                                     })
                                     ->values()
                                     ->toArray());
        $report = $flare->createReport($e);
        Mail::to('dev@toph.de')->send(new ExceptionMail($flat, $report->toArray()));
    }
}
