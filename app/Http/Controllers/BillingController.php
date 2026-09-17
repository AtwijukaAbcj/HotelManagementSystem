<?php

namespace App\Http\Controllers;

use App\Models\billing;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\addrooms;
use App\Services\ReceiptDeliveryService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BillingController extends Controller
{


    public function billing_report(){

        $data = billing::with('guest')->latest('updated_at')->get();

        return response()
            ->view('admin.billing.billreport', compact('data'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }



    public function billing()
    {
        $guests = Guest::with(['stays' => function ($query) {
            $query->with('room')->latest('id');
        }])->orderBy('first_name')->get();
        $latestBooking = Booking::orderBy('created_at', 'desc')->first();
        $rooms = addrooms::orderBy('room_number')->get();
        $selectedRoom = null;

        if ($latestBooking) {
            $selectedRoom = addrooms::where('room_number', $latestBooking->room_number)
                ->where('room_type', $latestBooking->room_type)
                ->first();
        }

        $stayDays = $latestBooking
            ? Carbon::parse($latestBooking->arrival_date)->diffInDays(Carbon::parse($latestBooking->departure_date))
            : null;

        $defaultPrice = $selectedRoom ? $selectedRoom->price : null;

        $guestProfiles = [];
        foreach ($guests as $guest) {
            $stay = $guest->stays->first();
            $room = $stay?->room;

            $guestProfiles[$guest->id] = [
                'name' => $guest->full_name,
                'email' => $guest->email,
                'room_number' => $room?->room_number,
                'room_type' => $room?->room_type,
                'price' => $room?->price,
                'stay_days' => $stay?->arrival_date && $stay?->departure_date
                    ? Carbon::parse($stay->arrival_date)->diffInDays(Carbon::parse($stay->departure_date))
                    : null,
            ];
        }

        return view('admin.billing.billing', compact('latestBooking', 'stayDays', 'rooms', 'defaultPrice', 'guests', 'guestProfiles'));
    }


    public function savebill(Request $request, ReceiptDeliveryService $receiptDelivery)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|in:cash,card,bank_transfer,mobile_money,online',
            'guest_id' => 'nullable|integer|exists:guests,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'room_type' => 'required|string|max:255',
            'room_number' => 'required|string|max:50',
            'no_of_days_stay' => 'required|integer|min:1|max:3650',
            'price' => 'required|numeric|min:0',
        ]);

        $guest = !empty($validated['guest_id']) ? Guest::findOrFail($validated['guest_id']) : null;
        $validated['email'] = $validated['email'] ?: $guest?->email;

        // Create a new new instance and set the values from the form
        $data = new billing;


        $data->fill($validated);
        $data->billing_date = Carbon::now()->format('Y-m-d');
        $data->billing_time = Carbon::now()->format('H:i:s');
        $data->total = (int) $validated['no_of_days_stay'] * (float) $validated['price'];



        // Save the data to the database
        $data->save();

        $data->receipt_number = 'BILL-' . str_pad((string) $data->id, 6, '0', STR_PAD_LEFT);
        $data->saveQuietly();

        if ($data->email) {
            $receiptDelivery->send(
                document: $data,
                email: $data->email,
                documentType: 'Billing Receipt',
                documentNumber: $data->receipt_number,
                recipientName: $data->name,
                amount: number_format((float) $data->total, 2),
                documentDate: $data->billing_date . ' ' . $data->billing_time,
                receiptUrl: route('billing.print', ['billing' => $data]),
                emailColumn: 'email',
            );
        }

        return redirect()->to(url('/billing_report'))->with('message', 'bill recorded sucessfully!');
    }

    public function printBill(billing $billing)
    {
        $billing->refresh();

        return response()
            ->view('admin.billing.bill-print', compact('billing'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }


    public function deletebillrecord($id)
    {
        $data = billing::find($id);
        $data->delete();
        return redirect()->to(url('/billing_report'))->with('message', 'Data deleted successfully!')->with('alert-class', 'alert-delete');

    }

    public function updatebillrecord($id)
    {
        $data = billing::findOrFail($id);
        $guests = Guest::with(['stays' => function ($query) {
            $query->with('room')->latest('id');
        }])->orderBy('first_name')->get();
        $rooms = addrooms::orderBy('room_number')->get();
        $guestProfiles = [];
        foreach ($guests as $guest) {
            $stay = $guest->stays->first();
            $room = $stay?->room;
            $guestProfiles[$guest->id] = [
                'name' => $guest->full_name,
                'email' => $guest->email,
                'room_number' => $room?->room_number,
                'room_type' => $room?->room_type,
                'price' => $room?->price,
                'stay_days' => $stay?->arrival_date && $stay?->departure_date
                    ? Carbon::parse($stay->arrival_date)->diffInDays(Carbon::parse($stay->departure_date))
                    : null,
            ];
        }
        $latestBooking = Booking::latest()->first();
        $stayDays = $data->no_of_days_stay;
        $defaultPrice = $data->price;

        return view('admin.billing.billing', compact('data', 'latestBooking', 'stayDays', 'rooms', 'defaultPrice', 'guests', 'guestProfiles'));
    }

    public function update_billdata_confirm(Request $request, $id, ReceiptDeliveryService $receiptDelivery)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|in:cash,card,bank_transfer,mobile_money,online',
            'guest_id' => 'nullable|integer|exists:guests,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'room_type' => 'required|string|max:255',
            'room_number' => 'required|string|max:50',
            'no_of_days_stay' => 'required|integer|min:1|max:3650',
            'price' => 'required|numeric|min:0',
        ]);

        $data = billing::findOrFail($id);
        $guest = !empty($validated['guest_id']) ? Guest::findOrFail($validated['guest_id']) : null;
        $validated['email'] = $validated['email'] ?: $guest?->email;
        $data->fill($validated);
        $data->billing_date = $data->billing_date ?: Carbon::now()->format('Y-m-d');
        $data->billing_time = $data->billing_time ?: Carbon::now()->format('H:i:s');
        $data->total = (int) $validated['no_of_days_stay'] * (float) $validated['price'];

        // Save the updated data to the database
        $data->save();
        $data->refresh();

        if ($data->email) {
            $receiptDelivery->send(
                document: $data,
                email: $data->email,
                documentType: 'Billing Receipt',
                documentNumber: $data->receipt_number ?: 'BILL-' . str_pad((string) $data->id, 6, '0', STR_PAD_LEFT),
                recipientName: $data->name,
                amount: number_format((float) $data->total, 2),
                documentDate: $data->billing_date . ' ' . $data->billing_time,
                receiptUrl: route('billing.print', ['billing' => $data]),
                emailColumn: 'email',
            );
        }

        // Redirect to a success page or perform any other desired action
        return redirect()->to(url('/billing_report'))->with('message', 'Data updated successfully!');
    }

    

    
}
