const { request } = require('express')
const { response } = require('express')
const express = require('express')
const session = require('express-session')
const app = express()
const port = 8080

app.use(session({
    secret: 'djdfaojsapkdvousdn',
    resave: true,
    saveUninitialized: true
}))

app.get('/login',(req,res)=>{
    req.session.loggedIn = true
    res.redirect('/home')
})

app.get('/home',(req,res)=>{
    if(req.session.loggedIn){
        res.send("Coglione")
    }
    res.send("Not coglione")
})


app.listen(port, ()=>{
    console.log('App listening on port '+port)
})