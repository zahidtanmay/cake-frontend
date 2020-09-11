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
    <script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer></script>
    <script src="https://unpkg.com/vue-recaptcha@latest/dist/vue-recaptcha.min.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>

<div id="app">
    <div class="content" v-if="state === 0">
        <h1>Register</h1>

        <template v-if="errors.length > 0">
            <template v-for="error in errors"><div style="color: red;" >{{error}}</div></template>

        </template>

        <div id="message">
            <form id="frmContact" action="" method="POST" novalidate="novalidate">
                <div class="label">Name:</div>
                <div class="field">
                    <input type="text" id="name" name="name" placeholder="enter your name here" title="Please enter your name" class="required" aria-required="true" v-model="name" required>
                </div>
                <div class="label">Email:</div>
                <div class="field">
                    <input type="email" id="email" name="email" placeholder="enter your email address here" title="Please enter your email address" class="required email" v-model="email" aria-required="true" required>
                </div>
                <div class="label">Password:</div>
                <div class="field">
                    <input type="password" id="password" name="password" placeholder="enter your phone number here" title="Please enter your phone number" class="required phone" aria-required="true" v-model="password" required>
                </div>
                <div class="label">Confirm Password:</div>
                <div class="field">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="enter your phone number here" title="Please enter your phone number" class="required phone" aria-required="true" v-model="confirm_password" required>
                </div>
<!--                <div class="g-recaptcha" data-sitekey="--><?php //echo SITE_KEY; ?><!--"></div>-->
                <vue-recaptcha
                    ref="recaptcha"
                    @verify="onCaptchaVerified"
                    @expired="onCaptchaExpired"
                    sitekey="<?php echo SITE_KEY; ?>">
                </vue-recaptcha>
                <div id="mail-status"></div>
                <button type="Submit" id="send-message" style="clear:both;" :disabled="!name || !email || !password || !confirm_password || !recapcha" @click.prevent="submit">Register</button>
            </form>
            <div id="loader-icon" style="display:none;"><img src="img/loader.gif" /></div>
        </div>


    </div><!-- content -->
</div><!-- central -->


<script>
    var app = new Vue({
        el: '#app',
        components: {
            'vue-recaptcha': VueRecaptcha
        },
        data: {
            state: 0,
            name: '',
            email: '',
            password: '',
            confirm_password: '',
            recapcha: '',
            status: '',
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
            submit: function () {
                let url = <?php echo json_encode(BASE_URL); ?>;
                let con = this
                axios.post(`http://${url}/users.json`, { name: con.name, email: con.email, password: con.password, confirm_password: con.confirm_password })
                    .then(function (response) {
                        con.status = response.data.message
                        this.name = ''
                        this.email = ''
                        window.location.href = "/login.php";

                    })
                    .catch(function (error) {
                        // handle error
                        console.log(error.response.data.message);
                        con.errors = JSON.parse(error.response.data.message)
                    })
                // this.status = "submitting";
                // this.$refs.recaptcha.execute();
            },
            onCaptchaVerified: function (recaptchaToken) {
                const self = this;
                this.recapcha = recaptchaToken
                self.status = "submitting";
                // self.$refs.recaptcha.reset();
                console.log(recaptchaToken)
                // axios.post("https://vue-recaptcha-demo.herokuapp.com/signup", {
                //     email: self.email,
                //     password: self.password,
                //     recaptchaToken: recaptchaToken
                // }).then((response) => {
                //     self.sucessfulServerResponse = response.data.message;
                // }).catch((err) => {
                //     self.serverError = getErrorMessage(err);
                //
                //
                //     //helper to get a displayable message to the user
                //     function getErrorMessage(err) {
                //         let responseBody;
                //         responseBody = err.response;
                //         if (!responseBody) {
                //             responseBody = err;
                //         }
                //         else {
                //             responseBody = err.response.data || responseBody;
                //         }
                //         return responseBody.message || JSON.stringify(responseBody);
                //     }
                //
                // }).then(() => {
                //     self.status = "";
                // });


            },
            onCaptchaExpired: function () {
                this.$refs.recaptcha.reset();
            }
        },
    })
</script>
</body>
</html>