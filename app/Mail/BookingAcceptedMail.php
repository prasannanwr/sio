<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public ?string $customerName;
    public ?string $deliveryManName;

    public function __construct(Order $order, ?string $customerName = null, ?string $deliveryManName = null)
    {
        $this->order = $order;
        $this->customerName = $customerName;
        $this->deliveryManName = $deliveryManName;
    }

    public function build()
    {
        $pdfData = $this->contractData();

        $pdf = Pdf::loadView('pdfs.transport-contract', $pdfData);

        return $this->subject(env('APP_NAME') . ' - Your booking has been accepted')
            ->view('emails.booking-accepted')
            ->attachData(
                $pdf->output(),
                'transport-contract-' . $this->order->tracking_id . '.pdf',
                ['mime' => 'application/pdf']
            );
    }

    /**
     * Assemble the dynamic values for the transport-contract PDF from the order.
     */
    protected function contractData(): array
    {
        $order = $this->order;
        $package = $order->items()->first();

        // Plate number comes from the vehicle assigned to the order's delivery man.
        $vehicle = Vehicle::where('delivery_man_id', $order->dispatcher_id)
            ->where('status', Vehicle::STATUS_ACTIVE)
            ->first()
            ?: Vehicle::where('delivery_man_id', $order->dispatcher_id)->first();

        $clientName = $this->customerName
            ?: (optional(optional($order->customer)->user)->name ?: $order->pickup_name);

        return [
            'order'              => $order,
            'logoSrc'            => $this->logoDataUri(),
            'flags'              => $this->flags(),
            'clientName'         => $clientName,
            'driverName'         => $this->deliveryManName ?: optional($order->deliveryMan)->name,
            'contractReference'  => $order->tracking_id,
            'pickupAddress'      => $this->addressLine($order->pickup_address1, $order->pickup_city, $order->pickup_country),
            'deliveryAddress'    => $this->addressLine($order->delivery_address1, $order->delivery_city, $order->delivery_country),
            'receiverName'       => $order->delivery_name,
            'receiverIdType'     => '', // not captured yet
            'receiverIdNumber'   => '', // not captured yet
            'cargoDescription'   => optional($package)->item_desc ?: $order->cond_of_goods,
            'cargoWeight'        => optional($package)->weight,
            'cargoPieces'        => optional($package)->qty,
            'vehiclePlate'       => optional($vehicle)->plate_number,
            'signatureToken'     => '', // not generated yet
        ];
    }

    /**
     * Inline SVG flags (Italy, Canada, Nigeria) as base64 data URIs — dompdf renders SVG,
     * and emoji flags do not, so we draw them as images.
     *
     * @return array<string,string>
     */
    protected function flags(): array
    {
        $italy = '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="20" viewBox="0 0 3 2">'
            . '<rect width="3" height="2" fill="#fff"/><rect width="1" height="2" fill="#009246"/>'
            . '<rect x="2" width="1" height="2" fill="#ce2b37"/></svg>';

        $nigeria = '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="20" viewBox="0 0 3 2">'
            . '<rect width="3" height="2" fill="#fff"/><rect width="1" height="2" fill="#008751"/>'
            . '<rect x="2" width="1" height="2" fill="#008751"/></svg>';

        $canada = '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="15" viewBox="0 0 96 48">'
            . '<rect width="96" height="48" fill="#fff"/><rect width="24" height="48" fill="#d52b1e"/>'
            . '<rect x="72" width="24" height="48" fill="#d52b1e"/>'
            . '<path fill="#d52b1e" d="M48 9l2.6 4.9c.3.5.8.5 1.3.2l1.9-1-.7 5.2c-.2 1 .5 1 .9.6l3.3-3.4.9 1.8c.2.4.6.4 1 .3'
            . 'l3.4-.7-1.2 3.4c-.2.6.1.8.4 1l1.2.6-5.9 4.6c-.4.3-.3.7-.2 1.1l.5 1.6-5.6-.5c-.5 0-.8.3-.8.6l.2 5.5h-1.6'
            . 'l.2-5.5c0-.3-.3-.6-.8-.6l-5.6.5.5-1.6c.1-.4.2-.8-.2-1.1L38 27.4l1.2-.6c.3-.2.6-.4.4-1l-1.2-3.4 3.4.7'
            . 'c.4.1.8.1 1-.3l.9-1.8 3.3 3.4c.4.4 1.1.4.9-.6l-.7-5.2 1.9 1c.5.3 1 .3 1.3-.2z"/></svg>';

        return [
            'it' => 'data:image/svg+xml;base64,' . base64_encode($italy),
            'ca' => 'data:image/svg+xml;base64,' . base64_encode($canada),
            'ng' => 'data:image/svg+xml;base64,' . base64_encode($nigeria),
        ];
    }

    /**
     * Embed the SIOSHIPPING logo as a base64 data URI for reliable rendering in the PDF.
     */
    protected function logoDataUri(): ?string
    {
        $path = public_path('landing/assets/img/gallery/sioshipping_logo.png');

        if (! is_file($path)) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }

    protected function addressLine(?string $address, ?string $city, ?string $country): string
    {
        return implode(', ', array_filter([
            trim((string) $address) ?: null,
            trim((string) $city) ?: null,
            trim((string) $country) ?: null,
        ]));
    }
}
