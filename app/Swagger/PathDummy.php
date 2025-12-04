<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/_dummy",
 *     tags={"Dummy"},
 *     summary="Dummy endpoint untuk memenuhi requirement Swagger",
 *     description="Tidak digunakan, hanya agar Swagger-PHP tidak error",
 *     @OA\Response(
 *         response=200,
 *         description="OK"
 *     )
 * )
 */
class PathDummy {}
