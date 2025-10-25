<?php

namespace Src\Services\SpTrans;

abstract class Endpoint
{
    const AUTH = "/Login/Autenticar";
    const GET_LINES = "/Linha/Buscar";
    const GET_LINES_DIRECTION = "/Linha/BuscarLinhaSentido";
    const GET_STOPS = "/Parada/Buscar";
    const GET_STOP_LINE = "/Parada/BuscarParadasPorLinha";
    const GET_POSITIONS = "/Posicao";
    const GET_LINE_POSITIONS =  "/Posicao/Linha";
}