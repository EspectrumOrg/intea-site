<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>chat test</title>

</head>
<body>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>

<div id="chatbox">
  <div id="friendslist">
      <div id="topmenu">
          <span class="friends"></span>
            <span class="chats"></span>
            <span class="history"></span>
        </div>
        
        <div id="friends">
          <div class="friend">
              <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/1_copy.jpg" />
                <p>
                  <strong>Miro Badev</strong>
                  <span>mirobadev@gmail.com</span>
                </p>
                <div class="status available"></div>
            </div>
            
            <div class="friend">
              <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/2_copy.jpg" />
                <p>
                  <strong>Martin Joseph</strong>
                  <span>marjoseph@gmail.com</span>
                </p>
                <div class="status away"></div>
            </div>
            
            <div class="friend">
              <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/3_copy.jpg" />
                <p>
                  <strong>Tomas Kennedy</strong>
                  <span>tomaskennedy@gmail.com</span>
                </p>
                <div class="status inactive"></div>
            </div>
            
            <div class="friend">
              <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/4_copy.jpg" />
                <p>
                  <strong>Enrique Sutton</strong>
                  <span>enriquesutton@gmail.com</span>
                </p>
                <div class="status inactive"></div>
            </div>
            
            <div class="friend">
              <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/5_copy.jpg" />
                <p>
                <strong>  Darnell Strickland</strong>
                  <span>darnellstrickland@gmail.com</span>
                </p>
                <div class="status inactive"></div>
            </div>
            
            <div id="search">
              <input type="text" id="searchfield" value="Search contacts..." />
            </div>
            
        </div>                
        
    </div>  
</div>
    <div class="center">
        <div class="chat">
            <div class="contact bar">
                <div class="pic stark"></div>
                <div class="name">Tony Stark</div>
                <div class="seen">Today at 12:56</div>
            </div>
            
            <div class="messages" id="chat">
                <div class="time">Today at 11:41</div>
                <div class="message parker">Hey, man! What's up, Mr Stark? 👋</div>
                <div class="message stark">Kid, where'd you come from?</div>
                <div class="message parker">Field trip! 🤣</div>
                <div class="message parker">Uh, what is this guy's problem, Mr. Stark? 🤔</div>
                <div class="message stark">Uh, he's from space, he came here to steal a necklace from a wizard.</div>
            </div>
            
            <div class="input">
                <i class="fas fa-camera"></i>
                <i class="far fa-laugh-beam"></i>
                <input type="text" placeholder="Type your message here!">
                <i class="fas fa-microphone"></i>
            </div>
        </div>
    </div>
</div>
</body>
<style>


body {
  background: #f0f1f2;
  font:12px "Open Sans", sans-serif; 
}
#chatbox{
  width:290px;
  background:#fff;
  border-radius:6px;
  overflow:hidden;
  height:484px;
  position:absolute;
  top:100px;
  left:30%;
  margin-left:-155px;
}

#friendslist{
  position:absolute;
  top:0;
  left:0;
  width:290px;
  height:484px;
}
#topmenu{
  height:69px;
  width:290px;
  border-bottom:1px solid #d8dfe3;  
}
#topmenu span{
  float:left; 
  width: 96px;
    height: 70px;
    background: url("https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/top-menu.png") -3px -118px no-repeat;
}
#topmenu span.friends{margin-bottom:-1px;}
#topmenu span.chats{background-position:-95px 25px; cursor:pointer;}
#topmenu span.chats:hover{background-position:-95px -46px; cursor:pointer;}
#topmenu span.history{background-position:-190px 24px; cursor:pointer;}
#topmenu span.history:hover{background-position:-190px -47px; cursor:pointer;}
.friend{
  height:70px;
  border-bottom:1px solid #e7ebee;    
  position:relative;
}
.friend:hover{
  background:#f1f4f6;
  cursor:pointer;
}
.friend img{
  width:40px;
  border-radius:50%;
  margin:15px;
  float:left;
}
.floatingImg{
  width:40px;
  border-radius:50%;
  position:absolute;
  top:0;
  left:12px;
  border:3px solid #fff;
}
.friend p{
  padding:15px 0 0 0;     
  float:left;
  width:220px;
}
.friend p strong{
  font-weight:600;
  font-size:15px;
  color:#597a96;  

}
.friend p span{
  font-size:13px;
  font-weight:400;
  color:#aab8c2;
}
.friend .status{
  background:#26c281;
  border-radius:50%;  
  width:9px;
  height:9px;
  position:absolute;
  top:31px;
  right:17px;
}
.friend .status.away{background:#ffce54;}
.friend .status.inactive{background:#eaeef0;}
#search{
  background:#e3e9ed url("https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/search.png") -11px 0 no-repeat;
  height:60px;
  width:290px;
  position:absolute;
  bottom:0;
  left:0;
}
#searchfield{
  background:#e3e9ed;
  margin:21px 0 0 55px;
  border:none;
  padding:0;
  font-size:14px;
  font-family:"Open Sans", sans-serif; 
  font-weight:400px;
  color:#8198ac;
}
#searchfield:focus{
   outline: 0;
}









































        .center {

            display: flex;
            width: 30%;
            max-width: 1200px;
            height: 66.5vh;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-left: 580px;
            margin-top: 100px;
        }

        /* Contacts Section */

        .pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            background: #7f8c8d;
        }

        .pic.rogers { background: #e74c3c; }
        .pic.stark { background: #3498db; }
        .pic.banner { background: #2ecc71; }
        .pic.thor { background: #f39c12; }
        .pic.danvers { background: #9b59b6; }

        .badge {
            position: absolute;
            right: 15px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .message {
            font-size: 12px;
            color: #bdc3c7;
        }

        /* Chat Section */
        .chat {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .contact.bar {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: #ecf0f1;
            border-bottom: 1px solid #bdc3c7;
        }

        .contact.bar .pic {
            width: 40px;
            height: 40px;
        }

        .contact.bar .name {
            margin-left: 10px;
            flex: 1;
        }

        .seen {
            color: #7f8c8d;
            font-size: 12px;
        }

        .messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8f9fa;
        }

        .time {
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
            margin: 10px 0;
        }

        .message {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 15px;
            margin-bottom: 10px;
            position: relative;
        }

        .message.parker {
            background: #3498db;
            color: white;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
        }

        .message.stark {
            background: #e74c3c;
            color: white;
            align-self: flex-end;
            margin-left: auto;
            border-bottom-right-radius: 5px;
        }

        .typing {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            margin: 0 2px;
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-1 { animation-delay: -0.32s; }
        .typing-2 { animation-delay: -0.16s; }

        @keyframes typing {
            0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }

        .input {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: #ecf0f1;
            border-top: 1px solid #bdc3c7;
        }

        .input i {
            color: #7f8c8d;
            margin: 0 10px;
            cursor: pointer;
            font-size: 20px;
        }

        .input input {
            flex: 1;
            padding: 12px 15px;
            border: none;
            border-radius: 25px;
            outline: none;
            font-size: 14px;
        }
    </style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
