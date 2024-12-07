<?php 
    /**
     * Función para securizar los inputs que mande el cliente
     */
    function securizar($data){
        return htmlspecialchars(stripslashes(trim($data)));
    }
?>