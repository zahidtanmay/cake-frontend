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
    <div class="content">
        <h1>Welcome</h1> <button v-if="state===3" @click="logout">Logout</button>
        <template v-if="state===3">
            <br/>

            <h1>Users List</h1>
            <template v-for="user in users">
                <div>Id: {{user.id}}</div>
                <div>Name: {{user.name}}</div>
                <div>Email: {{user.email}}</div>
                <hr/>
            </template>

            <br/>

            <button :disabled="!meta.prevPage" @click="goPrevious()">Previous Page</button>
            <button :disabled="!meta.nextPage" @click="goNext()">Next Page</button>
        </template>

        <template v-else>
            <a href="/login.php">Login</a>
            <a href="/register.php">Register</a>
        </template>




    </div><!-- content -->
</div><!-- central -->


<script>
    var app = new Vue({
        el: '#app',
        data: {
            state: 0,
            user: null,
            users: [],
            meta: []
        },
        mounted() {
            let url = <?php echo json_encode(BASE_URL); ?>;
            let token = localStorage.getItem('token')
            let con = this
            if (token) {
                this.user = JSON.parse(localStorage.getItem('user'))
                this.state = 3
                axios.get(`http://${url}/users.json?token=${token}`)

                    .then(function (response) {
                        con.users = response.data.users
                        con.meta = response.data.meta
                    })

                    .catch(function (error) {
                        console.log(error.response.data.code)
                        if (error.response.data.code === 401) {
                            localStorage.clear()
                            window.location.href = "/index.php"
                        }
                    })
            } else {
                this.state = 0
            }
        },

        methods: {
            goPrevious() {
                if (this.meta.prevPage) {
                    let url = <?php echo json_encode(BASE_URL); ?>;
                    let token = localStorage.getItem('token')
                    let con = this
                    const page = this.meta.page - 1
                    axios.get(`http://${url}/users.json?page=${page}&token=${token}`)

                        .then(function (response) {
                            con.users = response.data.users
                            con.meta = response.data.meta
                        })

                        .catch(function (error) {
                            console.log(error.response)
                            if (error.response.data.code === 401) {
                                localStorage.clear()
                                window.location.href = "/index.php"
                            }
                        })
                }

            },

            goNext() {
                if (this.meta.nextPage) {
                    let url = <?php echo json_encode(BASE_URL); ?>;
                    let token = localStorage.getItem('token')
                    let con = this
                    const page = this.meta.page + 1
                    axios.get(`http://${url}/users.json?page=${page}&token=${token}`)

                        .then(function (response) {
                            con.users = response.data.users
                            con.meta = response.data.meta
                        })

                        .catch(function (error) {
                            console.log(error.response)
                            if (error.response.data.code === 401) {
                                localStorage.clear()
                                window.location.href = "/index.php"
                            }
                        })
                }
            },

            logout() {
                localStorage.clear()
                this.state = 0
            }
        }
    })
</script>
</body>
</html>