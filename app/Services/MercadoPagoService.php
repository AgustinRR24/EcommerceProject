<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Resources\Preference;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    private $client;

    public function __construct()
    {
        // Configurar MercadoPago SDK
        $accessToken = config('mercadopago.access_token');

        if (!$accessToken) {
            throw new \Exception('MercadoPago access token not configured');
        }

        Log::info('MercadoPago token configured:', ['token_prefix' => substr($accessToken, 0, 20) . '...']);

        MercadoPagoConfig::setAccessToken($accessToken);

        $this->client = new PreferenceClient();
    }

    /**
     * Crear una preferencia de pago (versión simplificada según documentación)
     */
    public function createPreference($items, $backUrls = null, $externalReference = null)
    {
        try {
            $preferenceData = [
                "items" => $items
            ];

            // Agregar URLs de retorno si se proporcionan (sin auto_return por problemas de compatibilidad)
            if ($backUrls) {
                $preferenceData["back_urls"] = $backUrls;
            }

            // Agregar referencia externa si se proporciona
            if ($externalReference) {
                $preferenceData["external_reference"] = $externalReference;
            }

            // Agregar configuración de notificaciones (solo si no es localhost)
            $webhookUrl = url('/checkout/webhook');
            if (!str_contains($webhookUrl, '127.0.0.1') && !str_contains($webhookUrl, 'localhost')) {
                $preferenceData["notification_url"] = $webhookUrl;
            }

            Log::info('Creating MercadoPago preference with data:', $preferenceData);

            $preference = $this->client->create($preferenceData);

            Log::info('MercadoPago preference created successfully:', [
                'id' => $preference->id,
                'init_point' => $preference->init_point
            ]);

            return [
                'success' => true,
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point
            ];

        } catch (MPApiException $e) {
            Log::error('MercadoPago API Exception:', [
                'message' => $e->getMessage(),
                'response' => $e->getApiResponse() ? $e->getApiResponse()->getContent() : 'No response',
                'status_code' => $e->getApiResponse() ? $e->getApiResponse()->getStatusCode() : 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => $e->getApiResponse() ? $e->getApiResponse()->getStatusCode() : 'unknown'
            ];
        } catch (\Exception $e) {
            Log::error('General Exception in MercadoPago service:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener información de un pago
     */
    public function getPayment($paymentId)
    {
        try {
            $paymentClient = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $paymentClient->get($paymentId);

            return [
                'success' => true,
                'payment' => $payment
            ];
        } catch (MPApiException $e) {
            Log::error('MercadoPago Payment API Exception:', [
                'payment_id' => $paymentId,
                'message' => $e->getMessage(),
                'response' => $e->getApiResponse() ? $e->getApiResponse()->getContent() : 'No response',
                'status_code' => $e->getApiResponse() ? $e->getApiResponse()->getStatusCode() : 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('General Exception getting payment:', [
                'payment_id' => $paymentId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Formatear items para MercadoPago (según documentación)
     */
    public function formatItems($cartItems)
    {
        $items = [];

        foreach ($cartItems as $item) {
            // Ajustar precio para testing (máximo $10,000 USD para sandbox)
            $price = (float) $item['price'];
            if ($price > 10000) {
                $price = 10000; // Límite para sandbox
            }

            $items[] = [
                "title" => $item['product']['name'],
                "quantity" => (int) $item['quantity'],
                "unit_price" => $price,
                "currency_id" => "ARS"
            ];
        }

        return $items;
    }

    /**
     * Calcular total de items
     */
    public function calculateTotal($items)
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['unit_price'] * $item['quantity'];
        }
        return $total;
    }
}