<?php

namespace App\Http\Requests;

use App\Absence;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AbsenceRequest extends FormRequest
{

    /** @var Absence absence */
    protected $absence = null;


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->has('id')) {
            return false;
        }
        if (null === $this->absence) {
            $this->absence = Absence::find($this->get('id'));
        }
        return $this->user()->can('update', $this->absence);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'from' => 'required|date_format:d.m.Y',
            'to' => 'required|date_format:d.m.Y',
            'reason' => 'required|string',
            'replacement_notes' => 'nullable|string',
            'workflow_status' => 'int|in:' . Absence::STATUS_NEW,
        ];
        if ($this->route()->getName() == 'absences.store') {
            $rules['user_id'] = 'required|exists:users,id';
        }

        if (null === $this->absence) {
            $this->absence = Absence::find($this->get('id'));
        }
        // self-administrator?
        if ($this->user()->can('selfAdminister', $this->absence)) {
            $rules['workflow_status'] = 'nullable|int|in:' . join(
                    ',',
                    [
                        Absence::STATUS_SELF_ADMINISTERED,
                        Absence::STATUS_SELF_ADMINISTERED_AND_APPROVED
                    ]
                );
            $rules['approved_at'] = 'nullable|date_format:d.m.Y';
        } else {
            $mayCheck = $this->absence->user->vacationAdmins->pluck('id')->contains($this->user()->id);
            $mayApprove = $this->absence->user->vacationApprovers->pluck('id')->contains($this->user()->id);
            if ($mayCheck) {
                $rules['workflow_status'] = 'nullable|int|in:' . join(
                        ',',
                        [Absence::STATUS_NEW, Absence::STATUS_CHECKED]
                    );
                $rules['admin_notes'] = 'nullable|string';
                $rules['admin_id'] = 'nullable|int|exists:users,id';
            }
            if ($mayApprove) {
                $rules['workflow_status'] = 'nullable|int|in:' . join(
                        ',',
                        [
                            Absence::STATUS_NEW,
                            Absence::STATUS_CHECKED,
                            Absence::STATUS_APPROVED
                        ]
                    );
                $rules['approver_notes'] = 'nullable|string';
                $rules['approver_id'] = 'nullable|int|exists:users,id';
            }
        }
        return $rules;
    }

    /**
     * Get the validated data
     * @return array|void
     */
    public function validated()
    {
        $data = parent::validated();

        $data['from'] = Carbon::createFromFormat('d.m.Y', $data['from'])->setTime(0, 0, 0);
        $data['to'] = Carbon::createFromFormat('d.m.Y', $data['to'])->setTime(23, 59, 59);

        if (isset($data['approved_at'])) {
            if (strlen($data['approved_at']) == 10) $data['approved_at'] .= ' 0:00:00';
            $data['approved_at'] = Carbon::parse($data['approved_at']);
        }

        // check if admin/approver id needs to be set
        if (isset($data['workflow_status']) && ($this->absence->workflow_status != $data['workflow_status'])) {
            if ($data['workflow_status'] == Absence::STATUS_NEW) {
                $data['admin_id'] = null;
                $data['approver_id_id'] = null;
                $data['checked_at'] = null;
                $data['approved_at'] = null;
            }
            if ($data['workflow_status'] >= Absence::STATUS_CHECKED && (null === $this->absence->admin_id)) {
                $data['admin_id'] = $this->user()->id;
                $data['checked_at'] = Carbon::now();
            }
            if ($data['workflow_status'] == Absence::STATUS_APPROVED && (null === $this->absence->approver_id)) {
                $data['approver_id'] = $this->user()->id;
                $data['approved_at'] = Carbon::now();
            }
        }
        return $data;
    }

    /**
     * @return Absence
     */
    public function getAbsence(): ?Absence
    {
        return $this->absence;
    }

    /**
     * @param Absence $absence
     */
    public function setAbsence(?Absence $absence): void
    {
        $this->absence = $absence;
    }


}
