<Response>
   	<Message><?php

function calculate_string( $mathString )    {
    $mathString = trim($mathString);     // trim white spaces
    $mathString = ereg_replace ('[^0-9\+-\*\/\(\) ]', '', $mathString);    // remove any non-numbers chars; exception for math operators
 
    $compute = create_function("", "return (" . $mathString . ");" );
    return 0 + $compute();
}

if(isset($_POST["Body"])){


	
	$data = preg_split('/\s+/', $_POST["Body"]);
	switch(strtolower($data[0])){
	case "weather":
		$location = $data[2];
		if (strtolower($data[1]) != "in"){
			$location = $data[1];
		}else if ($data[1] == null || $data[1] == "" || $data[1] == " " || !isset($data[1])){
			$location = $_POST["FromZip"];
		}

		$string = file_get_contents("http://api.wunderground.com/api/1611a35c86206b42/conditions/lang:EN/q/".$location.".json");
		$json = json_decode($string, true);

		if(isset($json["current_observation"]["weather"])){
			echo $json["current_observation"]["weather"];
		}else{
			echo "Format: Weather in [zip code] or [State/City]";
		}

		if(isset($json["current_observation"]["temperature_string"])){
			echo " - ".$json["current_observation"]["temperature_string"];
		}

		break;

	case "call":
		if (strtolower($data[1]) == "in"){
			exec('echo "php /var/www/html/call.php '.$_POST["From"].'" | at now + '.$data[2]." ".$data[3]);
			
			echo "Ok I will call.";
			
		}
		break;

	case "math":
		$equation = substr($_POST["Body"], 4);
		echo calculate_string($equation);
		break;

	case "image":
		if($data[1] == "of"){
			$q = substr($_POST["Body"], 9);
			$string = file_get_contents("https://www.googleapis.com/customsearch/v1?key=AIzaSyDArQK85aidIMBZ2_cggSGTzWurOZjA8Pc&cx=005595739271832921248:yy2pmz_bo-0&searchType=image&q=".urlencode($q));
			$json = json_decode($string, true);

			//echo urlencode("https://www.googleapis.com/customsearch/v1?key=AIzaSyDArQK85aidIMBZ2_cggSGTzWurOZjA8Pc&cx=005595739271832921248:yy2pmz_bo-0&searchType=image&q=".$q);


			
			if(isset($json["items"][0])){
				$url = $json["items"][0]["link"];
				echo "<Media>".$url."</Media>";
			}
		}
		break;

	case "define":
		if(isset($data[1])){
			$string = file_get_contents("http://www.dictionaryapi.com/api/v1/references/collegiate/xml/".$data[1]."?key=121e0b9b-d6e4-4436-b20d-bcafb66387e5");
			$xml = new SimpleXMLElement($string);
			echo $xml->entry[0]->def[0]->dt;
		}
		break;

	case "thesaurus":
		if(isset($data[1])){
			$string = file_get_contents("http://www.dictionaryapi.com/api/v1/references/thesaurus/xml/".$data[1]."?key=ac08f2b2-1941-48c2-a690-811aa9b26626");
			$xml = new SimpleXMLElement($string);
			echo strip_tags($xml->entry[0]->sens[0]->rel);
		}
		break;

	case "wiki":
		$q = substr($_POST["Body"], 5);
		$string = file_get_contents("https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles=".rawurlencode($q));
		$json = json_decode($string, true);
		if (isset($json["query"]["pages"])){
			foreach($json["query"]["pages"] as $page){
				$chunks = explode("||||",wordwrap($page["extract"],155,"||||",false));
				$total = count($chunks);


				$i = 0;
				foreach($chunks as $page_ => $chunk){
					if ($i != 0){
						echo "<Message>";
					}

					$message = sprintf("(%d/%d) %s",$i+1,$total,$chunk);
					
					echo $message."</Message>";
					$i = $i + 1;
				}

				echo "</Response>";
				die;
				
				break;
			}
		}
		break;

	case "echo":
		echo substr($_POST["Body"], 5);
		break;

	case "text":
		if (strtolower($data[1]) == "in"){
			exec('echo "php /var/www/html/text.php '.$_POST["From"].' \"'."This your text you asked for.".'\"" | at now + '.$data[2]." ".$data[3]);
			
			echo "Ok I will send the text.";
			
		}else{
			exec('php /var/www/html/text.php '."+1".$data[1].' "'.substr($_POST["Body"], 16).'"');
			echo "Ok I will send the text.";
		}
		
		break;

	case "quote":
		if(strtolower($data[1]) == "rand"){
			$string = file_get_contents("http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=1");
			$json = json_decode($string, true);
			echo $json[0]["title"]." - ";
			echo html_entity_decode(strip_tags($json[0]["content"]));
		}
		break;

	case "say":
		$str = substr($_POST["Body"], 3);
		exec('php /var/www/html/requestCostumCall.php '.$_POST["From"].' "'.$str.'"');
		echo "Calling";
		break;

	case "commands":
		echo "weather [zip code] or [city/state]\ncall in [1 minute(s)]\nmath [1+1]\nimage of [description]\ndefine [word]\nthesaurus [word]\nwiki [search]\necho [anything]\ntext in [1 minute(s)]\ntext [phone-number message]\nquote rand\nsay [anything]";
		break;
	default:
		echo "Invalid Responce";
	}
}
else{
	echo "Invalid Responce";
}
   		
   	?></Message>
   	
</Response>
