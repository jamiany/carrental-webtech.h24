const { createApp, ref } = Vue

createApp({
    data: function() {
        let rentalDate = new Date();
        rentalDate.setDate(rentalDate.getDate() + 1);
        let rentalDateString = `${rentalDate.getFullYear()}-${rentalDate.getMonth()}-${rentalDate.getDate()}`
        return {
            carList: null,
            navigationItems: [{display: 'Information zur Webseite', route: 'information'}, {display: 'Buchen (Eingabebereich)', route: 'eingabe'}, {display: 'getätigte Buchungen (Ausgabebereich)', route: 'ausgabe'}],
            route: '',
            param: null,
            currentInput: {},
            name: '',
            datum: rentalDateString,
            dauer: 1,
            buchungsnummer: '',
            benutzer_id: '',
            bookings: [],
            httpErrorMsg: '',
            bookingFormNameValid: false,
            bookingFormDateValid: true,
            bookingFormDauerValid: true,
        }
    },
    methods: {
        format_date(value) {
            if (!value) return ''
            return new Date(value).toLocaleDateString("de-CH");
        },  
        myFunction() {
            var x = document.getElementById("demo");
            if (x.className.indexOf("w3-show") == -1) {
                x.className += " w3-show";
            } else {
                x.className = x.className.replace(" w3-show", "");
            }
        },
        setroute(path, param = null){
            this.route = path;
            this.param = param;
            window.location.hash = path;
            if(path == 'ausgabe'){
                this.loadBookings();
            }
        },
        book() {
            if(!(this.validateInputDate() && this.validateInputDauer() && this.validateInputName())) {
                return;
            }

            fetch('/backend?operation=book', {
                method: 'POST',
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({ benutzer_name: this.name, fahrzeug_id: +this.param.id, date: this.datum, dauer: this.dauer })
            }).then(x => {
                console.log(x);
                this.buchungsnummer = x.response?.buchungsnummer;
                document.getElementById('confirmBooking').style.display = 'block';
                this.name = '';
                this.datum = '';
                this.dauer = 0;
            })
            .catch(error => this.showHttpErrorDialog(error));
        },
        loadBookings() {
            this.benutzer_id = this.getCookie('user');
            fetch('/backend?operation=bookings')
                .then(r => r.json())
                .then(l => this.bookings = l.response)
                .catch(error => this.showHttpErrorDialog(error));
        },
        getCookie(cname) {
            let name = cname + "=";
            let decodedCookie = decodeURIComponent(document.cookie);
            let ca = decodedCookie.split(';');
            for(let i = 0; i <ca.length; i++) {
              let c = ca[i];
              while (c.charAt(0) == ' ') {
                c = c.substring(1);
              }
              if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
              }
            }
            return "";
        },
        handleError(response) {
        },
        showHttpErrorDialog(msg){
            console.error(msg);
            this.httpErrorMsg = msg;
            document.getElementById('httpError').style.display = "block";
        },
        drawCar() {
            const canvas = document.getElementById('carCanvas');
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = 'blue';
            ctx.fillRect(50, 100, 200, 50);
            ctx.fillStyle = 'blue';
            ctx.fillRect(90, 70, 120, 40);
            ctx.beginPath();
            ctx.arc(80, 160, 20, 0, Math.PI * 2, true);
            ctx.fillStyle = 'black';
            ctx.fill();
            ctx.beginPath();
            ctx.arc(220, 160, 20, 0, Math.PI * 2, true);
            ctx.fillStyle = 'black';
            ctx.fill();
            ctx.fillStyle = 'lightblue';
            ctx.fillRect(100, 80, 40, 30);
            ctx.fillRect(150, 80, 40, 30);
            ctx.beginPath();
            ctx.arc(50, 125, 10, 0, Math.PI * 2, true);
            ctx.fillStyle = 'yellow';
            ctx.fill();
            ctx.beginPath();
            ctx.arc(250, 125, 10, 0, Math.PI * 2, true);
            ctx.fillStyle = 'yellow';
            ctx.fill();
            ctx.fillStyle = 'black';
            ctx.fillRect(120, 110, 20, 5);
            ctx.fillRect(160, 110, 20, 5);
        },
        validateInputName() {
            let inputName = document.getElementById('input_name');
            let errorElement = document.getElementById('error_input_name');
            this.bookingFormNameValid = inputName.checkValidity();
            errorElement.innerText = this.bookingFormNameValid ? '' : inputName.validationMessage;
            return this.bookingFormNameValid;
        },
        validateInputDate() {
            let inputDate = document.getElementById('input_date');
            let errorElement = document.getElementById('error_input_date');
            this.bookingFormDateValid = inputDate.checkValidity();
            errorElement.innerText = this.bookingFormDateValid ? '' : inputDate.validationMessage;
            return this.bookingFormDateValid;
        },
        validateInputDauer() {
            let inputDauer = document.getElementById('input_dauer');
            let errorElement = document.getElementById('error_input_dauer');
            this.bookingFormDauerValid = inputDauer.checkValidity();
            errorElement.innerText = this.bookingFormDauerValid ? '' : inputDauer.validationMessage;
            return this.bookingFormDauerValid;
        }
    },
    created: function() {
        fetch('/backend?operation=list')
            .then(r => r.json())
            .then(l => this.carList = l.response)
            .catch(error => this.showHttpErrorDialog(error));
        fetch('/backend?operation=initcookie', { method: 'PUT' })
            .then(_ => this.loadBookings())
            .catch(error => this.showHttpErrorDialog(error));
        setTimeout(() => this.drawCar(), 500);
        let setRouteByHash = () => {
            if(window.location.hash) {
                this.setroute(window.location.hash.substring(1));
            }
        };
        setRouteByHash();
        window.addEventListener("hashchange", setRouteByHash);
    },
    setup() {
        const message = ref('Hello vue!')
        return {
            message
        }
    }
}).mount('#app')