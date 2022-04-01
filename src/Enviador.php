<?php

namespace Josefranciscocruzcorro\EnviaSri;

use nusoap_client;

class Enviador
{
    public static function enviar($xml,$ambiente)
    {
        # code...
        $mensaje = base64_encode($xml);
        $parametros = array(); 
        $parametros['xml'] = $mensaje;

        $servicio = "https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl";
        if ($ambiente == 2) {
            # code...
            $servicio = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl";
        }

        $client = new nusoap_client($servicio);
        $client->soap_defencoding = 'utf-8';

        $result = $client->call("validarComprobante", $parametros, "http://ec.gob.sri.ws.recepcion");


        
        return $result["comprobantes"]["comprobante"]["mensajes"]["mensaje"]["mensaje"];
        
    }
    
    public static function autorizar($claveAcceso,$ambiente)
    {
        # code...
        $parametros = array(); 
        $parametros['claveAccesoComprobante'] = $claveAcceso;

        $servicio = "https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl"; 
        if ($ambiente == 2) {
            # code...
            $servicio = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl"; 
        }

        $client = new nusoap_client($servicio);
        $client->soap_defencoding = 'utf-8';

        $result = $client->call("autorizacionComprobante", $parametros, "http://ec.gob.sri.ws.autorizacion");

        if (@$result['autorizaciones']["autorizacion"]) {
            # code...
            return $result['autorizaciones']["autorizacion"]["estado"];
        }

        return 'NO EXISTE';
    }

    public static function extraerComprobanteJSON($claveAcceso,$ambiente)
    {
        # code...
        $parametros = array(); 
        $parametros['claveAccesoComprobante'] = $claveAcceso;

        $servicio = "https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl"; 
        if ($ambiente == 2) {
            # code...
            $servicio = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl"; 
        }

        $client = new nusoap_client($servicio);
        $client->soap_defencoding = 'utf-8';

        $result = $client->call("autorizacionComprobante", $parametros, "http://ec.gob.sri.ws.autorizacion");

        $r2 = '';
        if (@$result['autorizaciones']["autorizacion"]) {
            # code...
            $r2 = $result['autorizaciones']["autorizacion"]["comprobante"];

            $r2 = utf8_encode($r2);
            $r2 = utf8_decode($r2);
            $r2 = str_replace("&lt;","<",$r2);
            $r2 = str_replace("&gt;",">",$r2);

            $xml = simplexml_load_string($r2);
            $r2 = json_encode($xml);
        }

        return $r2;
    }
}