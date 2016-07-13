<?php

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 17-6-2016
 * Time: 17:49
 */

namespace web\tmp\common\home;

use application\controller;

// Handle post

if( $_SERVER['REQUEST_METHOD'] === "POST" ):

    (new controller\Controller( "handler", "excel", "excel" ))->request();

elseif( $_SERVER['REQUEST_METHOD'] === "GET" ):

    $data    = unserialize(CMS_GET_DATA);
    $allowed = ['excelResponseData'];

    if( !empty( $data ) ):

        foreach( $data as $key => $value ):

            if( in_array( $key, $allowed ) ) ${$key} = $value;

        endforeach;

    endif;

endif;

$excelResponseData = ( !empty( $excelResponseData ) ? unserialize( $excelResponseData ) : "" );

?>
<div class="home">
    <div class="homeTitle">
        Welkom bij QuaRatio
    </div>
    <div class="calculator">
        <div class="calculator-Title">
            Pensioen berekening 2016
        </div>
        <div class="calculator-Intro">
            Voor dit test voorbeeld gebruiken we een boodschappenlijstje.
            <br>
            <ul>
                <li>Het Excel bestand met de berekeningen(calculator) wordt gekopieerd.</li>
                <li>Het boodschappenlijstje inclusief pinkosten worden door PHP in het Excel bestand ingevoerd.</li>
                <li>Het Excel bestand voert zijn berekeningen uit.</li>
                <li>PHP leest het berekende Excel bestand uit.</li>
                <li>Het totaal aantal producten en het totaal te betalen bedrag wordt getoont.</li>
                <li>Het gekopieerde bestand wordt verwijderd</li>
            </ul>
        </div>
        <div class="calculator-Form">
            <form action="<?php echo LITENING_SELF; ?>" method="post">
                <div class="calculator-FormBox">
                    <div class="calculator-FormBox-left">
                        Elstar appel(p.stuk)
                    </div>
                    <div class="calculator-FormBox-right">
                        <input type="text" name="appel" value="">
                    </div>
                </div>
                <div class="calculator-FormBox">
                    <div class="calculator-FormBox-left">
                        Halfje knip wit
                    </div>
                    <div class="calculator-FormBox-right">
                        <input type="text" name="knipwit" value="">
                    </div>
                </div>
                <div class="calculator-FormBox">
                    <div class="calculator-FormBox-left">
                        Roomboter(ongezouten) 250 gram
                    </div>
                    <div class="calculator-FormBox-right">
                        <input type="text" name="roomboter" value="">
                    </div>
                </div>
                <div class="calculator-FormBox">
                    <div class="calculator-FormBox-left">
                        Eieren doos(10 stuks)
                    </div>
                    <div class="calculator-FormBox-right">
                        <input type="text" name="ei" value="">
                    </div>
                </div>
                <div class="calculator-FormBox">
                    <br><br>
                    <div class="calculator-FormBox-left">
                        Pinkosten
                    </div>
                    <div class="calculator-FormBox-right">
                        <input type="text" name="pin" value="">
                    </div>
                </div>
                <div class="calculator-FormBox">
                    <input type="submit" name="submit" value="Wat moet ik betalen?">
                </div>
            </form>
        </div>
        <?php

        if( !empty( $excelResponseData ) ):

            foreach( $excelResponseData as $key => $value ):

                echo $key." = ".$value.'<br>';

            endforeach;

        endif;

        ?>
    </div>
</div>