<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\appointment\AppointmentStatus;
use App\Http\Helpers\user\UserService;
use App\Http\Resources\V1\AppointmentResource;
use App\Http\Utils\HttpStatusCode;
use App\Http\Utils\Message;
use App\Http\Utils\ResponseHelper;
use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;

class AppointmentController extends Controller
{
    use ResponseHelper;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::all();

        return new AppointmentResource($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $user = UserService::getUser();
        $validated_data = $request->validated();
        $appointment = $user->appointments()->create($validated_data);
        return new AppointmentResource($appointment);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }
        return new AppointmentResource($appointment->load('customer', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $validated_data = $request->validated();
        $appointment->update($validated_data);
        if (!$appointment) {
            return $this->json(Message::invalid(), HttpStatusCode::$UNPROCESSABLE_ENTITY);
        }
        return $this->json(Message::updateResource(), HttpStatusCode::$ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return $this->json(Message::notFound(), HttpStatusCode::$NOT_FOUND);
        }

        if ($appointment->is_removed == AppointmentStatus::$REMOVE) {
            return $this->json(Message::alreadyChanged(), HttpStatusCode::$UNPROCESSABLE_ENTITY);
        }

        $appointment->is_removed = AppointmentStatus::$REMOVE;
        $appointment->save();

        return $this->json(Message::updateResource(), HttpStatusCode::$ACCEPTED);
    }
}
