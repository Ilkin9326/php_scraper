<?
require __DIR__ . "/vendor/autoload.php";
require_once("vendor/fabpot/goutte/Goutte/Client.php");

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
?>
<style>
    .input_text{
        width: 23%;
    }

    .input_div{
        text-align: center;
    }


</style>
<!DOCTYPE html>
<html>
<head>
    <title>Scrape serps</title>
</head>
<body>
<form action="" method="post">
    <div class="input_div"><input type="text" name="txtInput" value="" data-error="1" maxlength="250"
                              aria-describedby="error_1"
           class="input_text" id="wordSearchTerms1" autofocus="autofocus" placeholder="Search for words in trade marks, owner names or trade mark numbers">

    <button id="qa-search-submit" ng-class="{disabled: !(vm.resultCount !== null || vm.hasImage)}" type="submit"
            name="btnSubmit"
            class="button fill green search-button" title="Search">
        <span>Search</span>
        </button></div>
</form>

</body>
</html>


<?

if (isset($_POST["txtInput"]) && $_POST["txtInput"] != "") {
    $term = $_POST["txtInput"];

    ini_set('max_execution_time', '500');
    $client = new Client(HttpClient::create(['timeout' => 500]));

    //search page define
    $crawler1 = $client->request('GET', 'https://search.ipaustralia.gov.au/trademarks/search/quick/result?q=' . $term);
    //Get total result from parser html.
    $count = $crawler1->filter('h2')->first();

    if($count->count() > 0){
        $totalres =$count->text();
    }

    //Each element we get result
    //print_r($count);
    if($totalres > 0){
        echo 'Total result is: <b>'.$totalres.'</b>';
        $sum = 0;
        $items = array();
        for ($i = 0; $i < $totalres; $i++) {
            $sum +=$i;
            $crawler2 = $client->request('GET', 'https://search.ipaustralia.gov.au/trademarks/search/quick/result?q='.$term.'&p='.$i);
            $result = $crawler2->filter('.js-mark-record')->each(function ($node){

                $number = $node->filter('.qa-tm-number')->first();
                if($number->count() >0){
                    $number = $number->text();
                }

                $name = $node->filter('.trademark')->first();
                if($name->count() >0){
                    $name = $name->text();
                }

                $class = $node->filter('.classes')->first();
                if($class->count() >0){
                    $class = $class->text();
                }

                $status = $node->filter('.status')->first();
                if($status->count() >0){
                    $status = $status->text();
                }



                $details_page_url = $node->filter('a')->first();
                if($details_page_url->count() >0){
                    $details_page_url = $details_page_url->text();
                }


                $items = array(
                    'number' =>  $number,
                    'logo_url' => '$img ?? null',
                    'name' => $name,
                    'classes' => $class,
                    'status' => $status,
                    'details_page_url' => "https://search.ipaustralia.gov.au"."".$details_page_url ?? null
                );

            });

        }
        echo '<pre>';
        var_dump($items);


    }else{
        echo "Empty list";
        exit();
    }

}else{
    echo "Please fill out this field";
    exit();
}

?>