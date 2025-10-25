<?php

namespace MobiCity\Services\SpTrans\Transformers;

use MobiCity\Core\Log;
use MobiCity\Services\SpTrans\SpTransService;

class LineTransformer
{
    public static function transform(array $data): array {
        return [
            'id' => isset($data['cl']) ? (int) $data['cl'] : null,
            'code' => $data['lt'] ?? null,
            'direction' => isset($data['sl']) ? (int) $data['sl'] : null,
            'type' => isset($data['tl']) ? (int) $data['tl'] : null,
            'isCircular' => isset($data['lc']) ? (bool) $data['lc'] : false,
            'mainTerminal' => $data['tp'] ?? null,
            'secondaryTerminal' => $data['ts'] ?? null,
            'displayName' => self::buildDisplayName($data),
            'shapeId' => $data['sid']
        ];
    }

    public static function transformCollection(array $collection) {
        $result = [];
        $collection = SpTransService::getLineShapeId($collection);
        Log::info(SpTransService::getLineShapeId($collection));
        foreach ($collection as $item) {
            $result[] = self::transform($item);
        }

        return $result;
    }

    private static function buildDisplayName(array $data) {
        if (empty($data['lt']) || empty('tp') || empty('ts')) {
            return null;
        }

        return sprintf('%s - %s / %s', $data['lt'], strtoupper($data['tp']), strtoupper($data['ts']));
    }
}