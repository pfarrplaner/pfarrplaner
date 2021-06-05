<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

namespace Tests\Builder;


use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TestCoverageTest extends TestCase
{

    /**
     * Tests whether there is a test for every route
     * @test
     */
    public function testAllRoutesHaveTests()
    {
        $routes = Route::getRoutes()->getRoutesByName();
        ksort($routes);
        $tests = $this->getAllTests();
        foreach ($tests as $test) {
            preg_match_all('/route\(\'(.*?)\'/', file_get_contents($test), $matches);
            if (is_array($matches)) {
                foreach ($matches[1] as $match) {
                    if (isset($routes[$match])) unset($routes[$match]);
                }
            }
        }
        if (count($routes)) {
            $this->output('');
            $this->output('WARNING: The following routes have no test coverage:');
            $this->output(join(', ', array_keys($routes)));
            $this->output('');
        }
        $this->assertCount(0, $routes);
    }

    protected function getAllTests($path = false) {
        $tests = [];
        foreach (['Unit', 'Feature'] as $testType) {
            foreach (glob(base_path('tests/'.$testType.'/*Test.php')) as $item) {
                $tests[] = $item;
            }
        }
        return $tests;
    }

}
