const express = require('express')
const session = require('express-session')
var bodyParser = require('body-parser')
var MySqlStore = require('express-mysql-session')(session)
var mysql = require('mysql')
var md5 = require('md5')
const { query } = require('express')
const app = express()
const port = 8080

var options = {
    host: 'localhost',
    port: 3306,
    user: 'root',
    password: '',
    database: 'session_test'
}

var sessionStore = new MySqlStore(options)

app.use(session({
    secret: 'Ai gatti piace il pesce',
    resave: true,
    saveUninitialized: true,
    store: sessionStore,
    maxAge: 86400000
}))

app.use(bodyParser.urlencoded({ extended: false }))

//Login,Signin,Home,Search,User,Edit_profile,Delete_profile

app.post('/login',(req,res)=>{
    if(req.session.loggedIn == false){
        var email = req.body.email
        var password = req.body.pswd
        var con = mysql.createConnection({
            host: 'localhost',
            user: 'root',
            password: '',
            database: 'mhgh'
        }).connect((err)=>{
            if(err) throw err;
            con.query("SELECT CU.password_salt as 'password_salt',CU.password_hash as 'password_hash',U.username as 'username' FROM credenziali_utenti CU JOIN utente U on CU.id = U.related_user_id WHERE email = '"+email+"'",(err,result,fields)=>{
                if(result){
                    if(md5(password+""+result[0].password_salt) = result[0].password_hash){
                        req.session.loggedIn = true
                        req.session.user = {
                            email : email,
                            username : result[0].username
                        }
                        res.type(200)
                        res.send("Login effetuato")
                    }
                }
            })
        })
    }else{
        res.type(200)
        res.send("Login effetuato")
    }
})

app.post('/signin',(req,res)=>{
    
})

app.get('/home',(req,res)=>{
    if(req.session.loggedIn){
        res.send("Coglione")
    }
    else{
        res.send("Not coglione")
    }
    
})


app.listen(port, ()=>{
    console.log('App listening on port '+port)
})