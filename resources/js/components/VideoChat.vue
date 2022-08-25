
<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12"  v-if="authusertipo =='admin' && mostrar_div">
                    <div class="card">
                        <div class="card-body">
                        <h2 class="text-center">Listado de Solicitudes</h2>
                        
                        <div class="table-responsive">
                            <table ref="tabla_users" width="100%" style="color: black"  class="table table-hover table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                                <thead>
                                    <tr>
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" aria-sort="descending" style="width: 10%;">Id</th>
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" aria-sort="descending" style="width: 20%;">Cedula</th>
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" aria-sort="descending" style="width: 50%;">Nombre</th>
                                       
                                        <th class="sorting_desc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" aria-sort="descending" style="width: 20%;"></th>
                                       
                                    
                                    </tr>
                                </thead>
                                <tbody>
                                
                                    <tr v-for="user in allusers" :key="user.id">
                                        <td>{{ user.id }}</td>
                                        <td>{{ user.cedula }}</td>
                                        <td>{{ user.name }}</td>
                                       
                                        <td>
                                            <div class="btn-group" role="group">                                        
                                               
                                                <button type="button" class="btn btn-primary mr-2 btn-sm" @click="placeVideoCall(user.id, user.name)">
                                                    Llamar                                             
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2"></div>
                <div v-if="authusertipo =='Invitado' && mostrar_div" class="col-md-8 " style="margin-top:32px">
                   
                    <div class="card">
                        <div class="card-header" style="background-color: #0066ffbd;color:white"><i class="fa fa-video-camera"></i>  Solicitud de turno via videollamada </div>

                        <div class="card-body">
                            <form method="POST">
                                
                                <div class="form-group row">
                                    <label for="email" class="col-md-3 col-form-label text-md-right">Usuario</label>

                                    <div class="col-md-8">
                                        <textarea id="cedula" rows="1" disabled autocomplete="off" class="form-control " name="cedula" >{{datouser}} {{datousername}}</textarea>

                                    </div>
                                </div>

                                
                                <div class="form-group row mb-0">
                                    <div class="col-md-11 offset-md-3">
                                        <button type="button" class="btn btn-primary btn-sm" @click="solictar_usuario()">
                                            <i class="fa fa-video-camera"></i> Solicitar
                                            
                                        </button>

                                    </div>
                                </div>
                            </form>
                        </div>
            
                    </div>
                </div>
                 <div class="col-md-2"></div>
                

            </div>

            <!--Lugar Video Call-->
            <div class="row mt-5" id="video-row2">
                <div class="col-12 video-container" v-if="callPlaced">

                    <video ref="userVideo" muted playsinline autoplay class="cursor-pointer" :class="isFocusMyself === true ? 'user-video' : 'partner-video'" @click="toggleCameraArea"/>

                    <video ref="partnerVideo" playsinline autoplay class="cursor-pointer" :class="isFocusMyself === true ? 'partner-video' : 'user-video'" @click="toggleCameraArea" v-if="videoCallParams.callAccepted"
                    />

                    <div class="partner-video" v-else>
                        <div v-if="callPartner" class="column items-center q-pt-xl">
                            <div class="col q-gutter-y-md text-center">
                                <p class="q-pt-md">
                                    <strong>{{ callPartner }}</strong>
                                </p>
                                <p>Llamando...</p>
                            </div>
                        </div>
                    </div>

                    <div class="action-btns">
                        <button type="button" class="btn btn-info" @click="toggleMuteAudio">
                        {{ mutedAudio ? "Unmute" : "Silenciar" }} </button>
                        <button type="button" class="btn btn-primary mx-1" @click="toggleMuteVideo" >
                        {{ mutedVideo ? "Mostar Video" : "OcultarVideo" }} </button>
                        <button type="button" class="btn btn-danger" @click="endCall"> Finalizar </button>
                    </div>

                </div>
            </div>
            <!-- Fin of Lugar Video Call  -->

            <!-- LLamada entrante  -->
            <div class="row" v-if="incomingCallDialog" >
                <div class="col card" id="card_llamada_entrante">
                    
                    <audio src="https://soundbible.com/mp3/analog-watch-alarm_daniel-simion.mp3" ref="eventAudio" autoplay></audio>
                    <p class="text-center" style="margin-top: 32px;">
                        LLamada Entrante <strong>{{ callerDetails.name }}</strong>
                    </p>
                    <div class="" role="group">
                        <center>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"  @click="declineCall" >
                                Rechazar
                            </button>

                            <button type="button" class="btn btn-success ml-2" @click="acceptCall">
                                Aceptar
                            </button>
                        </center>
                    </div>
                </div>
            </div>
            <!-- Fin LLamada entrante  -->
            
        </div>
    </div>
</template>

<script>


import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
Vue.use(VueToast);

import * as $ from 'jquery'
import dt from 'datatables.net-bs4'
$.fn.DataTable = dt



// import datatable from 'datatables.net-bs4'
import Peer from "simple-peer";
import { getPermissions } from "../helpers";

export default {
    props: [
        "allusers",
        "authuserid",
        "turn_url",
        "turn_username",
        "turn_credential",
        "authusertipo",
        "cedula",
        "motivo",
        "datouser",
        "datousername"
        
    ],
    data() {
        return {
            user_recibir:false,
            mostrar_div: true,
            isFocusMyself: true,
            callPlaced: false,
            callPartner: null,
            mutedAudio: false,
            mutedVideo: false,
            incomingCallDialog:false,
            user_a_llamar:"",
            videoCallParams: {
                users: [],
                stream: null,
                receivingCall: false,
                caller: null,
                callerSignal: null,
                callAccepted: false,
                channel: null,
                peer1: null,
                peer2: null,
            
            },
            
        };
    },

    mounted() {
            this.initializeChannel(); // this initializes laravel echo
            this.initializeCallListeners(); // subscribes to video presence channel and listens to video events
            this.tabla_usuario();    
          
    },
    computed: {
       
        incomingCallDialog2() {

            console.log("receivingCall "+this.videoCallParams.receivingCall)
            console.log("videoCallParams "+this.videoCallParams.videoCallParams)
            console.log("authuserid "+this.authuserid)
            console.log("videoCallParams.caller "+this.videoCallParams.caller)

            if (
                this.videoCallParams.receivingCall &&
                this.videoCallParams.caller !== this.authuserid
            ) {
                this.mostrar_div=false;
                this.user_recibir=true;
                return true;
            }
            return false;
           
          
        },

        callerDetails() {
            if (
                this.videoCallParams.caller &&
                this.videoCallParams.caller !== this.authuserid
            ) {
                const incomingCaller = this.allusers.filter(
                (user) => user.id === this.videoCallParams.caller
                );

                return {
                id: this.videoCallParams.caller,
                name: `${incomingCaller[0].name}`,
                };
            }
            return null;
        },
    },

    methods: {
       
        tabla_usuario(){
            
            this.$nextTick(()=>{
                this.dt = $(this.$refs.tabla_users).DataTable();
             
                $(this.$refs.tabla_users).DataTable({
                    
                    "destroy":true,
                   
                    order: [[ 0, "asc" ]],
                    searching:true,
                    info:false,
                    pageLength: 5,
                    sInfoFiltered:false,
                    "language":  {
                        "lengthMenu": 'Mostrar <select class="">'+
                                            '<option value="5">5</option>'+
                                            '<option value="10">10</option>'+
                                            '<option value="20">20</option>'+
                                            '<option value="30">30</option>'+
                                            '<option value="40">40</option>'+
                                            '<option value="-1">Todos</option>'+
                                    '</select> ',
                        "search": "",
                        "searchPlaceholder": "Ingrese un criterio de busqueda",
                        "zeroRecords": "No se encontraron registros coincidentes",
                        "infoEmpty": "No hay registros para mostrar",
                        "infoFiltered": " - filtrado de MAX registros",
                        "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        "paginate": {
                            "previous": "Anterior",
                            "next": "Siguiente"
                        }
                    }
                });
                        
            })

           },
        //cuadno la llamada sea realizada por un soliciitante de turno
        async solictar_usuario(){

            this.mostrar_div=false;
            let name="ADMINISTRADOR";
            this.callPlaced = true;
            this.callPartner = name;
            await this.getMediaPermission();
            this.videoCallParams.peer1 = new Peer({
                initiator: true,
                trickle: false,
                stream: this.videoCallParams.stream,
                config: {
                    iceServers: [
                    {
                        urls: this.turn_url,
                        username: this.turn_username,
                        credential: this.turn_credential,
                    },
                    ],
                },
            });

            this.videoCallParams.peer1.on("signal", (data) => {
                console.log(data);
                // enviar señal de llamada de usuario (no logueado)
                axios
                .post("/video/call-solicitante", {
                    user_to_call: 0, //mando cero para que en el backend me busque al administrador
                    signal_data: data,
                    from: this.authuserid,
                })
                .then((repuesta) => {
                    if(repuesta.data.ocupado=='S'){
                       
                        this.$toast.open({ 
                            message: 'En este momento el administrador se encuentra en otro llamada, intentelo más tarde',
                            type: 'error',
                            duration: 10000,
                            dismissible: true,
                            queue: false,
                            position: 'top-right',
                            onClick: this.onClick,
                            onDismiss: this.onDismiss
                        })

                        setTimeout(() => {
                            location. reload();
                        }, 5000);

                        // location. reload();
                        // this.endCall();
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            });

            this.videoCallParams.peer1.on("stream", (stream) => {
                console.log("call streaming");
                if (this.$refs.partnerVideo) {
                    this.$refs.partnerVideo.srcObject = stream;
                }
            });

            let llamada_conectada=false;
            this.videoCallParams.peer1.on("connect", () => {
                console.log("peer connected");
                llamada_conectada=true;
            });

            this.videoCallParams.peer1.on("error", (err) => {
                console.log(err);
            });

            this.videoCallParams.peer1.on("close", () => {
              
                console.log("call closed calle111r");
               
            });

            this.videoCallParams.channel.listen("StartVideoChat", ({ data }) => {
                console.log("www "+data);
                this.user_a_llamar=data.userToCall;
                if (data.type === "callAccepted") {
                    if (data.signal.renegotiate) {
                        console.log("renegotating");
                    }
                    if (data.signal.sdp) {
                        this.videoCallParams.callAccepted = true;
                        const updatedSignal = {
                        ...data.signal,
                        sdp: `${data.signal.sdp}\n`,
                        };
                        this.videoCallParams.peer1.signal(updatedSignal);
                    }
                }
            });
           
        },

        initializeChannel() {
        this.videoCallParams.channel = window.Echo.join("presence-video-channel");
        },

        getMediaPermission() {
            return getPermissions()
            .then((stream) => {
                this.videoCallParams.stream = stream;
                if (this.$refs.userVideo) {
                    this.$refs.userVideo.srcObject = stream;
                }
            })
            .catch((error) => {
                console.log(error);
            });
        },

        initializeCallListeners() {
          
            this.videoCallParams.channel.here((users) => {
                this.videoCallParams.users = users;
            });

            this.videoCallParams.channel.joining((user) => {
                // check user availability
                const joiningUserIndex = this.videoCallParams.users.findIndex(
                (data) => data.id === user.id
                );
                if (joiningUserIndex < 0) {
                this.videoCallParams.users.push(user);
                }
            });

            this.videoCallParams.channel.leaving((user) => {
                const leavingUserIndex = this.videoCallParams.users.findIndex(
                (data) => data.id === user.id
                );
                this.videoCallParams.users.splice(leavingUserIndex, 1);
            });
            // listen to incomming call
            this.videoCallParams.channel.listen("StartVideoChat", ({ data }) => {
                console.log(data);
                if (data.type === "incomingCall") {
                    // add a new line to the sdp to take care of error
                    const updatedSignal = {
                        ...data.signalData,
                        sdp: `${data.signalData.sdp}\n`,
                    };

                    this.videoCallParams.receivingCall = true;
                    this.videoCallParams.caller = data.from;
                    this.videoCallParams.callerSignal = updatedSignal;

                    if(data.userToCall==this.authuserid){
                                           
                        this.incomingCallDialog=true
                        this.mostrar_div=false;
                    }else{
                    //     this.incomingCallDialog=false
                    //    this.mostrar_div=true;
                    }

                   
                }
            });
        },

        async placeVideoCall(id, name) {
            this.mostrar_div=false;
            console.log(id)
            console.log(name)
            this.callPlaced = true;
            this.callPartner = name;
            await this.getMediaPermission();
            this.videoCallParams.peer1 = new Peer({
                initiator: true,
                trickle: false,
                stream: this.videoCallParams.stream,
                config: {
                    iceServers: [
                    {
                    urls: this.turn_url,
                    username: this.turn_username,
                    credential: this.turn_credential,
                    },
                    ],
                },
            });

            this.videoCallParams.peer1.on("signal", (data) => {
                console.log(data);
                // send user call signal
                axios
                .post("/video/call-user", {
                    user_to_call: id,
                    signal_data: data,
                    from: this.authuserid,
                })
                .then((repuesta) => {
                      if(repuesta.data.ocupado=='S'){
                       
                        this.$toast.open({ 
                            message: 'En este momento el usuario se encuentra en otro llamada, intentelo más tarde',
                            type: 'error',
                            duration: 10000,
                            dismissible: true,
                            queue: false,
                            position: 'top-right',
                            onClick: this.onClick,
                            onDismiss: this.onDismiss
                        })

                        setTimeout(() => {
                            location. reload();
                        }, 5000);

                        // location. reload();
                        // this.endCall();
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            });

            this.videoCallParams.peer1.on("stream", (stream) => {
                console.log("call streaming");
                if (this.$refs.partnerVideo) {
                    this.$refs.partnerVideo.srcObject = stream;
                }
            });
            let llamada_conectada=false;
            this.videoCallParams.peer1.on("connect", () => {
                console.log("peer connected");
                llamada_conectada=true
            });

            this.videoCallParams.peer1.on("error", (err) => {
                console.log(err);
            });

            this.videoCallParams.peer1.on("close", () => {
               
                console.log("call closed calle111r");
               
            });

            this.videoCallParams.channel.listen("StartVideoChat", ({ data }) => {
                console.log(data);
                if (data.type === "callAccepted") {
                    if (data.signal.renegotiate) {
                        console.log("renegotating");
                    }
                    if (data.signal.sdp) {
                        this.videoCallParams.callAccepted = true;
                        const updatedSignal = {
                        ...data.signal,
                        sdp: `${data.signal.sdp}\n`,
                        };
                        this.videoCallParams.peer1.signal(updatedSignal);
                    }
                }
            });
        },

        async acceptCall() {
            this.mostrar_div=false;
            this.incomingCallDialog=false;
            this.callPlaced = true;
            this.videoCallParams.callAccepted = true;
            await this.getMediaPermission();
            this.videoCallParams.peer2 = new Peer({
                initiator: false,
                trickle: false,
                stream: this.videoCallParams.stream,
                config: {
                iceServers: [
                    {
                    urls: this.turn_url,
                    username: this.turn_username,
                    credential: this.turn_credential,
                    },
                ],
                },
            });
            console.log()
            this.videoCallParams.receivingCall = false;
            this.videoCallParams.peer2.on("signal", (data) => {
                axios
                .post("/video/accept-call", {
                    signal: data,
                    to: this.videoCallParams.caller,
                })
                .then(() => {})
                .catch((error) => {
                    console.log(error);
                });
            });

            this.videoCallParams.peer2.on("stream", (stream) => {
                this.videoCallParams.callAccepted = true;
                this.$refs.partnerVideo.srcObject = stream;
            });

            this.videoCallParams.peer2.on("connect", () => {
                console.log("peer connected");
                this.videoCallParams.callAccepted = true;
            });

            this.videoCallParams.peer2.on("error", (err) => {
                console.log(err);
            });

            this.videoCallParams.peer2.on("close", () => {
                // alert("La otra persona ha finalizado la videollamada")
                console.log("call closed accepter");
            });
            console.log(this.videoCallParams.callerSignal);
            this.videoCallParams.peer2.signal(this.videoCallParams.callerSignal);
        },
        toggleCameraArea() {
            if (this.videoCallParams.callAccepted) {
                this.isFocusMyself = !this.isFocusMyself;
            }
        },
        getUserOnlineStatus(id) {
            const onlineUserIndex = this.videoCallParams.users.findIndex(
                (data) => data.id === id
            );
            if (onlineUserIndex < 0) {
                return "Offline";
            }
            return "Online";
        },
        declineCall() {
            console.log("llamada rechazada")
            location. reload()
            // this.videoCallParams.receivingCall = false;
            // this.mostrar_div=true;
          
          
        },

        toggleMuteAudio() {
            if (this.mutedAudio) {
                this.$refs.userVideo.srcObject.getAudioTracks()[0].enabled = true;
                this.mutedAudio = false;
            } else {
                this.$refs.userVideo.srcObject.getAudioTracks()[0].enabled = false;
                this.mutedAudio = true;
            }
        },

        toggleMuteVideo() {
            if (this.mutedVideo) {
                this.$refs.userVideo.srcObject.getVideoTracks()[0].enabled = true;
                this.mutedVideo = false;
            } else {
                this.$refs.userVideo.srcObject.getVideoTracks()[0].enabled = false;
                this.mutedVideo = true;
            }
        },

        stopStreamedVideo(videoElem) {
            const stream = videoElem.srcObject;
            const tracks = stream.getTracks();
            tracks.forEach((track) => {
                track.stop();
            });
            videoElem.srcObject = null;
        },
        endCall() {
            // if video or audio is muted, enable it so that the stopStreamedVideo method will work
            if (!this.mutedVideo) this.toggleMuteVideo();
            if (!this.mutedAudio) this.toggleMuteAudio();
            this.stopStreamedVideo(this.$refs.userVideo);
            console.log(this.authuserid)
            console.log(this.videoCallParams.caller)
            if(this.videoCallParams.caller!=null){
                if (this.authuserid === this.videoCallParams.caller) {
                    this.videoCallParams.peer1.destroy();
                } else {
                    this.videoCallParams.peer2.destroy();
                    location. reload()
                    
                }
            }else{
                this.videoCallParams.peer1.destroy();
            }
            this.videoCallParams.channel.pusher.channels.channels[
                "presence-presence-video-channel"
            ].disconnect();

            setTimeout(() => {
                this.callPlaced = false;
                this.mostrar_div=true;
            }, 100);

            axios
            .post("/video/end-call", {
                tipo: "close",
            })
            .then(() => {})
            .catch((error) => {
                console.log(error);
            });
            location. reload()
        },
    },
};
</script>

<style scoped>
    #video-row {
        width: 700px;
        max-width: 90vw;
    }

    #incoming-call-card {
        border: 1px solid #0acf83;
    }

    #card_llamada_entrante{
        height: 200px;

    }

    .video-container {
        width: 700px;
        height: 680px;
        max-width: 90vw;
        max-height: 80vh;
        margin: 0 auto;
        border: 1px solid #0acf83;
        position: relative;
        box-shadow: 1px 1px 11px #9e9e9e;
        background-color: #fff;
    }

    .video-container .user-video {
        width: 30%;
        position: absolute;
        left: 10px;
        bottom: 10px;
        border: 1px solid #fff;
        border-radius: 6px;
        z-index: 2;
    }

    .video-container .partner-video {
        width: 100%;
        height: 100%;
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        top: 0;
        z-index: 1;
        margin: 0;
        padding: 0;
    }

    .video-container .action-btns {
        position: absolute;
        bottom: 20px;
        left: 50%;
        margin-left: -50px;
        z-index: 3;
        display: flex;
        flex-direction: row;
    }
   

    /* Mobiel Styles */
    @media only screen and (max-width: 768px) {
        .video-container {
            height: 50vh;
        }
    }
</style>