<!DOCTYPE html>
<html>
<head>
  <title>Promises Practice</title>
</head>
<body>
  <h2>Enter your name:</h2>
  <input type="text" id="nameInput" placeholder="Your name">
  <button onclick="runE()">Run</button>
  <p id="message"></p>

  <script>
    // A. Reject if input is empty, resolve and greet
    function runA() {
      let name = document.getElementById("nameInput").value;
      let message = document.getElementById("message");

      let promise = new Promise(function(resolve, reject) {
        if (name === "") {
          reject("Name cannot be empty!");
        } else {
          resolve(name);
        }
      });

      promise
        .then(function(result) {
          message.textContent = "Good day, " + result + "!";
        })
        .catch(function(error) {
          message.textContent = error;
        });
    }

    // B. Reject if empty, resolve after 5 seconds
    function runB() {
      let name = document.getElementById("nameInput").value;
      let message = document.getElementById("message");

      let promise = new Promise(function(resolve, reject) {
        if (name === "") {
          reject("Name cannot be empty!");
        } else {
          setTimeout(function() {
            resolve(name);
          }, 5000);
        }
      });

      promise
        .then(function(result) {
          message.textContent = "Good day, " + result + "!";
        })
        .catch(function(error) {
          message.textContent = error;
        });
    }

    // C. Reject if empty, resolve after 7 seconds
    function runC() {
      let name = document.getElementById("nameInput").value;
      let message = document.getElementById("message");

      let promise = new Promise(function(resolve, reject) {
        if (name === "") {
          reject("Name cannot be empty!");
        } else {
          setTimeout(function() {
            resolve(name);
          }, 7000);
        }
      });

      promise
        .then(function(result) {
          message.textContent = "Good day, " + result + "!";
        })
        .catch(function(error) {
          message.textContent = error;
        });
    }

    // D. Reject if empty, resolve with uppercase
    function runD() {
      let name = document.getElementById("nameInput").value;
      let message = document.getElementById("message");

      let promise = new Promise(function(resolve, reject) {
        if (name === "") {
          reject("Name cannot be empty!");
        } else {
          resolve(name.toUpperCase());
        }
      });

      promise
        .then(function(result) {
          message.textContent = "Good day, " + result + "!";
        })
        .catch(function(error) {
          message.textContent = error;
        });
    }

    // E. Reject if empty OR less than 5 characters, resolve with uppercase
    function runE() {
      let name = document.getElementById("nameInput").value;
      let message = document.getElementById("message");

      let promise = new Promise(function(resolve, reject) {
        if (name === "") {
          reject("Name must not be empty!");
        } else if (name.length < 5){
          reject("Name should be at least 5 characters!");
        } else {
          resolve(name.toUpperCase());
        }
      });

      promise
        .then(function(result) {
          message.textContent = "Good day, " + result + "!";
        })
        .catch(function(error) {
          message.textContent = error;
        });
    }

    // F. Reject if empty OR less than 5 characters, resolve with reversed name
    function runF() {
      let name = document.getElementById("nameInput").value;
      let message = document.getElementById("message");

      let promise = new Promise(function(resolve, reject) {
        if (name === "") {
          reject("Name must not be empty!");
        } else if (name.length < 5){
          reject("Name should be at least 5 characters!");
        } else {
          let reversed = name.split("").reverse().join("");
          resolve(reversed);
        }
      });

      promise
        .then(function(result) {
          message.textContent = "Good day, " + result + "!";
        })
        .catch(function(error) {
          message.textContent = error;
        });
    }
  </script>
</body>
</html>
