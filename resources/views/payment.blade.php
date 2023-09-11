<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="facebook-domain-verification" content="okddszzoazvk30suviiz42fh1eku3b"/>
        <title>Laravel</title>
        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <script type="text/javascript" src="https://checkout.wompi.co/widget.js"></script>
        @vite('resources/css/app.css')



    </head>
    <body class="antialiased">
    <div class="overflow-y-auto sm:p-0 pt-4 pr-4 pb-20 pl-4 bg-gray-800">
        <div class="flex justify-center items-start text-center min-h-screen sm:block">
            <div class="bg-gray-500 transition-opacity bg-opacity-75"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">​</span>
            <div class= "inline-block text-left bg-gray-900 rounded-lg overflow-hidden align-bottom transition-all transform
            shadow-2xl sm:align-middle sm:max-w-xl sm:w-full">
                <div class="items-center w-full mr-auto ml-auto relative max-w-7xl md:px-12 lg:px-24">
                    <div class="grid grid-cols-1">
                        <div class="mt-4 mr-auto mb-4 ml-auto bg-gray-900 max-w-lg">
                            <div class="flex flex-col items-center pt-6 pr-6 pb-6 pl-6">
                                <img
                                    {{--https://images.pexels.com/photos/2379005/pexels-photo-2379005.jpeg?auto=compress&amp;cs=tinysrgb&amp;dpr=2&amp;w=500--}}
                                    src="{{  $avatar }}" class="flex-shrink-0 object-cover object-center btn- flex w-16 h-16 mr-auto -mb-8 ml-auto rounded-full shadow-xl">
                                <p class="mt-8 text-2xl font-semibold leading-none text-white tracking-tighter lg:text-3xl"> {{ $user->name }} {{$user->lastname}}</p>
                                <p class="mt-3 text-base leading-relaxed text-center text-gray-200">
                                    Hola tatuador, estas apunto de adquerir este plan mensual
                                    para tener mas oportunidades con tus usuarios, estaos muy
                                    agradecidos de que estes aqui con nosotros, esperemos que disfrutes tu proximo plan.
                                </p>
                                <div class="w-full mt-6">
                                    <button class="flex text-center items-center justify-center w-full pt-4 pr-10 pb-4 pl-10 text-base
                        font-medium text-white bg-indigo-600 rounded-xl transition duration-500 ease-in-out transform
                        hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="payment">Pagar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
    @vite('resources/js/app.js')
<script>
    const checkout = new WidgetCheckout({
        currency: "{{ strtoupper($payment->payment_currency)  }}",
        amountInCents: Number("{{ $payment->payment_amount  }}"),
        reference: "{{ $payment->payment_reference  }}",
        publicKey: 'pub_prod_EjcgGnHhlLadwW52vNRlX2iGM31wRYLM',
        // publicKey: 'pub_test_Q5yDA9xoKdePzhSGeVe9HAez7HgGORGf',
        redirectUrl: "{{ route('payment.success') }}",
        customerData: { // Opcional
            email:"{{ $user->email }}",
            fullName: "{{ $user->name }} {{ $user->lastname }}",
            phoneNumber: "{{ $user->phone }}",
            phoneNumberPrefix: '+57',
        }
    });
    const paymentButton = document.getElementById('payment');
    paymentButton.addEventListener('click', () => {
        checkout.open(function ( result ) {
            const transaction = result.transaction
            if (transaction.status === 'APPROVED') {
                axios(
                    {
                        url: "{{ route('payment.confirm') }}",
                        method: 'POST',
                        data: {
                            transaction: JSON.stringify(transaction),
                            user_id: {{ $user->id }},
                            payment_id: {{ $payment->id }},
                            plan_id: {{ $plan->id }},
                            status: transaction.status,
                            reference: transaction.reference,
                            transaction_id: transaction.id,
                        }
                    }
                ).then(function (response) {
                    console.log(response);
                    window.location.href = "{{ route('payment.success') }}";
                }).catch(function (error) {
                    console.log(error);
                });

            }
            console.log('Transaction ID: ', transaction.id)
            console.log('Transaction object: ', transaction)
        });
    });
</script>

</html>
