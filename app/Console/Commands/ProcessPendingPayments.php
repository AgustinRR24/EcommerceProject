<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\MercadoPagoService;
use App\Http\Controllers\CheckoutController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessPendingPayments extends Command
{
    protected $signature = 'payments:process-pending';
    protected $description = 'Process pending payments by checking MercadoPago status';

    public function handle()
    {
        $this->info('Checking pending payments...');

        // Buscar órdenes pendientes de las últimas 24 horas
        $pendingOrders = Order::where('payment_status', 'pending')
            ->where('created_at', '>', now()->subDay())
            ->get();

        $this->info("Found {$pendingOrders->count()} pending orders");

        $mercadoPagoService = app(MercadoPagoService::class);
        $processed = 0;

        foreach ($pendingOrders as $order) {
            try {
                // Intentar encontrar el pago en MercadoPago usando el external_reference
                $this->info("Processing order {$order->id} ({$order->order_number})");

                // Usar la lógica del CheckoutController para procesar
                $controller = new CheckoutController();

                // Simular un webhook call para esta orden
                $this->processOrderPayment($order, $mercadoPagoService, $controller);

                $processed++;

            } catch (\Exception $e) {
                $this->error("Error processing order {$order->id}: {$e->getMessage()}");
                Log::error("Error in ProcessPendingPayments for order {$order->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("Processed {$processed} orders");
        return 0;
    }

    private function processOrderPayment($order, $mercadoPagoService, $controller)
    {
        // Esta función simularía encontrar el pago por external_reference
        // En un entorno real, necesitarías hacer una búsqueda en MercadoPago
        // por external_reference para encontrar el payment_id

        $this->info("Order {$order->id} checked - would need MercadoPago search implementation");

        // Por ahora, solo log la orden que necesita ser verificada
        Log::info("Pending order to verify", [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'created_at' => $order->created_at
        ]);
    }
}