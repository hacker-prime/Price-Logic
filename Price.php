<?php  

// https://docs.google.com/document/d/1huuRJnT_M5o5e-_rEklAMYv1OZEaJZndirFeprfWCWU/edit

class Price{

    public function transportation($distance){
        
        $primary_base_rate_km = 5; //km
        $primary_base_rate_price = 250; //JMD

        $secondary_base_rate_km = 25; //km
        $secondary_base_rate_price = 150; //JMD

        $tertiary_base_rate_km = 235; //km Should I change this to the length of the island itself? The value was previously 125
        $tertiary_base_rate_price = 100; //JMD

        $paypal_fixed_fee = 0.49;
        $paypal_percentage_fee = 0.0349;

        $profit = $this->currency_conversion(100); //JMD This is how much money Phoenix Prime will get per transportation/taxi request

        $country_maximum_length_distance = 235; //km //https://www.britannica.com/place/Jamaica

        if($distance < $primary_base_rate_km){ //250 JMD

            $selling_price = $this->transporation_calculation($distance,$primary_base_rate_price,$paypal_fixed_fee,$paypal_percentage_fee,$profit);
            return $selling_price;

            }

        if($distance <= $secondary_base_rate_km && $distance > $primary_base_rate_km){ //150 JMD 

            $selling_price = $this->transporation_calculation($distance,$secondary_base_rate_price,$paypal_fixed_fee,$paypal_percentage_fee,$profit);
            return $selling_price;
             
        }


        if($distance <= $tertiary_base_rate_km && $distance > $secondary_base_rate_km){ //100 JMD - This would cover a distance from Ironshore,Fairview to Negril. The calculation would be 88.5 * 100 which gives us 8850. The problem with this is that the Ironshore taxi driver explained that the price would be 9,000 which would 150 JMD short. The company has to at the very least match the market rate. So I suppose we will have to add a fee of 150 JMD to make up for it. This 150 fee for such a long distance does yet include the markup required for the PayPal transaction fee/cost.
 
            $selling_price = $this->transporation_calculation($distance,$tertiary_base_rate_price,$paypal_fixed_fee,$paypal_percentage_fee,$profit);
            return $selling_price;
           
        }


    }

    public function currency_conversion($distance_TIMES_base_rate_price){

        // Real time currency converter

        $endpoint = 'convert';
        $access_key = '82824ad33994e9ef7c1292fee473330b';
        $from = 'JMD';
        $to = 'USD';
        $amount = $distance_TIMES_base_rate_price;

        // initialize CURL:
        $ch = curl_init('https://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.'&from='.$from.'&to='.$to.'&amount='.$amount.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // get the JSON data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $conversionResult = json_decode($json, true);

        return $conversionResult['result'];
        

    }

    public function transporation_calculation($distance,$base_rate_price,$paypal_fixed_fee,$paypal_percentage_fee,$profit){

        $distance_TIMES_base_rate_price = $distance * $base_rate_price; //JMD

        $selling_price_USD = $this->currency_conversion($distance_TIMES_base_rate_price); //6.466 - This value was derived using a distance of 4km // Fatal error: Uncaught Error: Call to undefined function currency_conversion() https://stackoverflow.com/questions/3225812/call-to-undefined-method-but-i-know-it-exists

        $selling_price_after_paypal_fixed_fee = $selling_price_USD - $paypal_fixed_fee; //5.975

        $paypal_percentage_fee_USD = $selling_price_after_paypal_fixed_fee * $paypal_percentage_fee; //0.2085275

        $paypal_transaction_fee = $paypal_fixed_fee + $paypal_percentage_fee_USD; //0.6985275

        $selling_price_PLUS_paypal_transaction_fee = $selling_price_USD + $paypal_transaction_fee; //7.1635275

        $selling_price_final = $selling_price_PLUS_paypal_transaction_fee + $profit; //7.8100275
        
        // echo $selling_price_final; 
        return $selling_price_final;

    }
    
}

// $price = new Price();

// $converstion = $price->currency_conversion(100); JMD 100

// echo $converstion; //USD 0.6483


?>  
