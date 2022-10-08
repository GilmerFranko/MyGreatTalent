<?php
$ImageUrl = str_replace(' ', '%20', $_GET['imagens']);
 ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="../rompecabezas/css/jqpuzzle.css" />
        <script type="text/javascript" src="../rompecabezas/js/jquery-1.7.2.js"></script>
        <?php include "../rompecabezas/js/jquery.jqpuzzle.php"; ?>
        <script type="text/javascript">

            //Seteos del rompecabezas.
            var settings = {
                shuffle: true,
                control: {
                    shufflePieces: true, // display 'Shuffle' button [true|false] 
                    confirmShuffle: false, // ask before shuffling [true|false] 
                    toggleOriginal: false, // display 'Ver Original' button  [true|false] 
                    toggleNumbers: true, // display 'Numbers' button [true|false] 
                    counter: true, // display moves counter [true|false] 
                    timer: true, // display timer (seconds) [true|false] 
                    pauseTimer: false
                }
            };
            //Textos que mostraran el rompecabezas.
            var myTexts = {
                shuffleLabel: 'Mezclar',
                toggleOriginalLabel: 'Ver Original',
                toggleNumbersLabel: 'Pista',
                confirmShuffleMessage: 'Desea mezclar la imagen?',
                movesLabel: 'Movimientos',
                secondsLabel: 'segundos'
            };
            $(document).ready(function() {
                $("#rompecabezas").jqPuzzle(settings, myTexts);
            });
        </script>
    </head>
    <body>
        <img id="rompecabezas" src="../<?php echo $ImageUrl; ?>" alt="" class="jqPuzzle" />
    </body>
</html>

<br/><br/>

