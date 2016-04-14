<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 14.04.2016
 * Time: 13:47
 */




$CON_ID = mysqli_connect($host, $user, $pass, $db )
or die ('Fehler : '.mysqli_connect_error($CON_ID));

function get_anzahl() {
    global $CON_ID;
    $sql="SELECT * from getraenke";
    $res=mysqli_query($CON_ID, $sql);
    $anzahl=mysqli_field_count($res);
    debug_to_console($anzahl);

    return $anzahl;
}


/**
 * Simple helper to debug to the console
 *
 * @param  Array, Object, String $data
 * @return String
 */
function debug_to_console( $data ) {

    $output = '';
    if ( is_array( $data ) ) {
        $output .= "<script>console.warn( 'Debug Objects with Array.' ); console.log( '" . implode( ',', $data) . "' );</script>";
    } else if ( is_object( $data ) ) {
        $data    = var_export( $data, TRUE );
        $data    = explode( "\n", $data );
        foreach( $data as $line ) {
            if ( trim( $line ) ) {
                $line    = addslashes( $line );
                $output .= "console.log( '{$line}' );";
            }
        }
        $output = "<script>console.warn( 'Debug Objects with Object.' ); $output</script>";
    } else {
        $output .= "<script>console.log( 'Debug Objects: {$data}' );</script>";
    }

    echo $output;
}


?>