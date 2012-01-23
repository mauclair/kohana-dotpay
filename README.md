Moduł płatności internetowych - dotpay.pl

1. Importujemy do bazy zawartość pliku: dotpay-schema-mysql.sql
2. Skonfiguruj w pliku konfiguracyjnym swój id,PIN z dotpay.pl 

3. Dodajemy pakiety do bazy.

    $payment = ORM::factory('payment');
    $payment->name          = 'Premium - 1 miesiąc';
    $payment->description   = 'Pakiet premium na okres 1 miesiąca';
    $payment->amount        = (float) 10; 
    $payment->control       = md5($payment->name);
    $payment->save();

    $payment = ORM::factory('payment');
    $payment->name          = 'Premium - 3 miesiące';
    $payment->description   = 'Pakiet premium na okres 3 miesięcy';
    $payment->amount        = (float) 30; 
    $payment->control       = md5($payment->name);
    $payment->save();

    $payment = ORM::factory('payment');
    $payment->name          = 'Premium - 12 miesięcy';
    $payment->description   = 'Pakiet premium na okres 12 miesięcy';
    $payment->amount        = (float) 120; 
    $payment->control       = md5($payment->name);
    $payment->save();

    Pole control jest to identyfikator płatności, musi być unikalny.


4. W kontrolerze utwórz 2 akcje np:

class Controller_Platnosc extends Controller_Frontend_Template {
        public function action_premium1mc() {
            $payment = ORM::factory('payment', 1); // Płatność - Premium 1 MC
            $this->template->content = Dotpay::instance()->pay($payment, 'adres@email.pl');
        }

        public function action_premium3mc() {
            $payment = ORM::factory('payment', 2); // Płatność - Premium 3 MC
            $this->template->content = Dotpay::instance()->pay($payment, 'adres@email.pl');
        }

        public function action_premium12mc() {
            $payment = ORM::factory('payment', 3); // Płatność - Premium 12 MC
            $this->template->content = Dotpay::instance()->pay($payment, 'adres@email.pl');
        }
        
        public function action_zaplacone() {
            $this->template->content = Dotpay::instance()->afterPay();
        }
}
5. Zaaktualizuj w pliku konfiguracyjnym parametr returnAction na 'platnosc/zaplacone'
6. Sprawdzanie płatności

$payment = ORM::factory('payment', 1);
$incomingPayments = $payment->incoming->find_all();

foreach ($incomingPayments as $k => $incomingPayment) {
    $stan = ($incomingPayment->status == 1) ? 'Zapłacone' : 'Brak płatności, lub coś nie tak';
    echo "$incomingPayment->email - $stan<br />";
}

