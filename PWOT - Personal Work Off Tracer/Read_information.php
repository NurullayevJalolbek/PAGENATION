<?php
class Read_information
{
    public string $arrival_time;
    public string $end_time;
    public function __construct($ARRIVAL_TIME, $END_TIME)
    {
        $this->arrival_time = $ARRIVAL_TIME;
        $this->end_time = $END_TIME;
    }
    public function Working_time()
            {
            $vaqt1 = new DateTime($this->arrival_time);
            $vaqt2 = new DateTime($this->end_time);

            $oraliq_vaqt = $vaqt1 -> diff($vaqt2);
            $num =  strval("$oraliq_vaqt->h : $oraliq_vaqt->i");
            $b1 = explode(":", $num);
            //var_dump( $b1[0]);
            if ($b1[0]>'9'){
                $b1[0] = '9';
                return  "$b1[0]:$b1[1]";
            }
            }

            
    public function Debt()
            {   
            $boshlanish_vaqti = new DateTime('08:00');
            $tugash_vaqti = new DateTime('17:00');

            $vaqt1 = new DateTime($this->arrival_time);
            $vaqt2 = new DateTime($this->end_time);    
           

            $oraliq_vaqt1 = $boshlanish_vaqti -> diff($vaqt1);
            $oraliq_vaqt2 = $vaqt2 -> diff($tugash_vaqti);
                
            $soat1 = $oraliq_vaqt1->h;
            $minut1 = $oraliq_vaqt1->i;

            $soat2 = $oraliq_vaqt2->h;
            $minut2 = $oraliq_vaqt2->i;
            $a1 = "$soat1:$minut1";
            $b1 = "$soat2:$minut2";

            list($soatt1,$minutt1)=explode(":",$a1);
            $a1SOAT = $soatt1 * 60 + $minutt1;

            list($soatt2,$minutt2)=explode(":",$b1);
            $b1SOAT = $soatt2 * 60 + $minutt2;

            $javob = $a1SOAT + $b1SOAT;

            $javobSOAT = floor($javob / 60);
            $javobMINUT = $javob % 60;
            $soatMinut = sprintf("%02d:%02d",$javobSOAT,$javobMINUT);
            return "$soatMinut";
            }
    
}
