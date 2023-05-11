<!DOCTYPE html>
<html>
<body>

<h2>Refund Form</h2>

<form id="refundForm">
  <label for="wallet">Wallet Address:</label><br>
  <input type="text" id="wallet" name="wallet"><br>
  <label for="amount">Refund Amount:</label><br>
  <input type="text" id="amount" name="amount"><br>
  <input type="submit" value="Submit">
</form> 

<script src="https://cdn.jsdelivr.net/npm/web3@1.3.4/dist/web3.min.js"></script>
<script>
window.addEventListener('load', async () => {
    if (window.ethereum) {
        window.web3 = new Web3(ethereum);
        try {
            await ethereum.enable();
        } catch (error) {
            console.error("User denied account access")
        }
    } else if (window.web3) {
        window.web3 = new Web3(web3.currentProvider);
    } else {
        console.log('No Metamask (or other wallet) detected');
    }

    document.getElementById('refundForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const wallet = document.getElementById('wallet').value;
        const amount = document.getElementById('amount').value;

        let message = "Please sign this message to confirm your refund request.";
        let accounts = await web3.eth.getAccounts();

        if (accounts[0] !== wallet) {
            alert('The provided wallet does not match the currently connected wallet in your Web3 provider.');
            return;
        }

        try {
            let signature = await web3.eth.personal.sign(message, wallet, "");
            console.log(`Signature: ${signature}`);

            let data = {
                wallet: wallet,
                amount: amount,
                signature: signature
            };

            // Send data to the PHP script
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

        } catch(err) {
            console.log(err);
        }
    });
});
</script>

<?php
    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the raw POST data
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if the file exists
        if (file_exists('data.txt')) {
            // Open the file for appending
            $file = fopen('data.txt', 'a') or die('Cannot open file:  '.$file); // implicitly creates file

            // Write the data to the file
            fwrite($file, print_r($data, true));
            
            // Close the file
            fclose($file);
        } else {
            // Write data to a new text file
            file_put_contents('data.txt', print_r($data, true));
        }
    }
?>

</body>
</html>
