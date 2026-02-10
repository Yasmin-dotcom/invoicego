<x-app-layout>

<div class="flex items-center justify-center min-h-[70vh]">
    <div class="bg-white p-10 rounded-2xl shadow text-center space-y-4">

        <h2 class="text-2xl font-bold">Upgrade to Pro</h2>
        <p class="text-gray-500">One-time payment to activate Pro</p>

        <button id="payBtn"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Upgrade Now ₹499
        </button>

    </div>
</div>


{{-- Razorpay script --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const btn = document.getElementById('payBtn');

    btn.addEventListener('click', async () => {

        btn.disabled = true;
        btn.innerText = "Processing...";

        try {

            /*
            ==========================
            STEP 1 → CREATE ORDER
            ==========================
            */
            const orderRes = await fetch("{{ route('payments.order') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                }
            });

            if (!orderRes.ok) {
                throw new Error("Order creation failed");
            }

            const order = await orderRes.json();

            /*
            ==========================
            STEP 2 → OPEN RAZORPAY
            ==========================
            */
            const options = {

                key: "{{ config('services.razorpay.key') }}",

                amount: order.amount,
                currency: order.currency || "INR",
                order_id: order.order_id,

                name: "SirfStyle Invoice",
                description: "Pro Plan Upgrade",

                handler: async function (response) {

                    try {

                        /*
                        ==========================
                        STEP 3 → VERIFY PAYMENT
                        ==========================
                        */
                        const verifyRes = await fetch("{{ route('payments.verify') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json",
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify(response)
                        });

                        const result = await verifyRes.json();

                        if (result.status === "success") {
                            window.location.href = "/dashboard";
                        } else {
                            throw new Error("Verification failed");
                        }

                    } catch (err) {
                        alert("Payment verification failed.");
                        resetBtn();
                    }
                },

                modal: {
                    ondismiss: resetBtn
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();

        } catch (err) {
            alert("Payment failed. Try again.");
            resetBtn();
        }

        function resetBtn() {
            btn.disabled = false;
            btn.innerText = "Upgrade Now ₹499";
        }

    });

});
</script>

</x-app-layout>
