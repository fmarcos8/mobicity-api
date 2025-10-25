<?php

namespace MobiCity\Services\SpTrans\Transformers;

class VehicleTransformer
{
    public static function transformPositionLine(array $lineData)
    {
        $vehicles = [];
        if (!empty($lineData['vs']) && is_array($lineData['vs'])) {
            foreach ($lineData['vs'] as $v) {
                $vehicles[] = self::transformVehicle($v);
            }
        }
        
        return [
            'time' => $lineData['hr'],
            'vehicles' => $vehicles,
            'vehicleCount' => count($vehicles)
        ];
    }

    public static function transformVehicle($v) 
    {
        return [
            'prefix' => isset($v['p']) ? (string)$v['p'] : null,
            'accessible' => isset($v['a']) ? (bool)$v['a'] : null,
            'timestamp' => $v['ta'] ?? null,
            'lat' => isset($v['py']) ? (float)$v['py'] : null,
            'lon' => isset($v['px']) ? (float)$v['px'] : null,
        ];
    }
}