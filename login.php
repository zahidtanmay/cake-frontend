<?php
require('constant.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Demo</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        .label {margin: 2px 0;}
        .field {margin: 0 0 20px 0;}
        .content {width: 960px;margin: 0 auto;}
        h1, h2 {font-family:"Georgia", Times, serif;font-weight: normal;}
        div#central {margin: 40px 0px 100px 0px;}
        @media all and (min-width: 768px) and (max-width: 979px) {.content {width: 750px;}}
        @media all and (max-width: 767px) {
            body {margin: 0 auto;word-wrap:break-word}
            .content {width:auto;}
            div#central {	margin: 40px 20px 100px 20px;}
        }
        body {font-family: 'Helvetica',Arial,sans-serif;background:#ffffff;margin: 0 auto;-webkit-font-smoothing: antialiased;  font-size: initial;line-height: 1.7em;}
        input, textarea {width:100%;padding: 15px;font-size:1em;border: 1px solid #A1A1A1;	}
        button {
            padding: 12px 60px;
            background: #5BC6FF;
            border: none;
            color: rgb(40, 40, 40);
            font-size:1em;
            font-family: "Georgia", Times, serif;
            cursor: pointer;
        }
        #message {  padding: 0px 40px 0px 0px; }
        #mail-status {
            padding: 12px 20px;
            width: 100%;
            display:none;
            font-size: 1em;
            font-family: "Georgia", Times, serif;
            color: rgb(40, 40, 40);
        }
        .error{background-color: #F7902D;  margin-bottom: 40px;}
        .success{background-color: #48e0a4; }
        .g-recaptcha {margin: 0 0 25px 0;}
    </style>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>

<div id="app">
    <div class="content" v-if="state === 0">
        <h1>Login</h1>

        <template v-if="errors.length > 0">
            <div style="color: red;">{{errors}}</div>
        </template>



        <div id="message">
            <form id="frmContact" action="" method="POST" novalidate="novalidate">
                <div class="label">Email:</div>
                <div class="field">
                    <input type="email" id="email" name="email" placeholder="Email" title="Email" class="required email" aria-required="true" v-model="email" required>
                </div>
                <div class="label">Password:</div>
                <div class="field">
                    <input type="password" id="phone" name="phone" placeholder="Password" title="Password" class="required phone" aria-required="true" v-model="password" required>
                </div>

                <button type="Submit" id="send-message" style="clear:both;" @click.prevent="login()" :disabled="!email || !password">Login</button>
            </form>
            <div id="loader-icon" style="display:none;"><img src="img/loader.gif" /></div>
        </div>


    </div><!-- content -->
</div><!-- central -->


<script>
    // const axios = require('axios');
    var app = new Vue({
        el: '#app',
        data: {
            state: 0,
            email: '',
            password: '',
            errors: []
        },
        mounted() {
            let token = localStorage.getItem('token')
            if (token) {
                this.state = 3
                window.location.href = "/index.php";
            } else {
                this.state = 0
            }
        },
        methods: {
            login() {
                let url = <?php echo json_encode(BASE_URL); ?>;
                let con = this
                axios.post(`http://${url}/login.json`, { email: con.email, password: con.password })
                    .then(function (response) {
                        localStorage.setItem('user', JSON.stringify(response.data.user))
                        localStorage.setItem('token', response.data.token)
                        window.location.href = "/index.php";
                    })
                    .catch(function (error) {
                        // handle error
                        console.log(error.response.data.message);
                        con.errors = error.response.data.message
                    })
            }
        }
    })
</script>
</body>
</html>