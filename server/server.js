const express = require('express')
const cors = require('cors')
const server = express()

const mongoose = require('mongoose')
mongoose.connect('mongodb://localhost:27017/server-test', { useNewUrlParser: true })

server.listen(4000, () => console.log('Server ready on port 4000'))
server.use(express.json())
server.use(cors())
server.set('json spaces', 2)

server.use('/cards', require('./routes/cards'))
server.use('/users', require('./routes/users'))
