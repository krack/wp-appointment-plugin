
{
    var app = new Vue({
        el: '#app',
        data: {
            services: [],
            categories: [],
            appointments: [],
            view: 'services',
            contact: false,
            selectedService: 0,
            workingDay: [],
            firstHourWeek: 23,
            firstMinWeek: 59,
            lastHourWeek: 0,
            lastMinWeek: 0,
            selectedStartDate: null,
            weekDiff: 0,
            name: null,
            phone: null
        },
        created: function () {
            this.loadServices();
        },
        methods: {
            loadServices() {

                this.$http.get('/wp-json/krack/v1/services').then((response) => {
                    this.services = response.body;
                    var categories = {};
                    for (var i = 0; i < this.services.length; i++) {
                        var categorie = this.services[i].categorie;
                        if (!categories[categorie]) {
                            categories[categorie] = {
                                "name": categorie,
                                "services": []
                            };
                        }
                        categories[categorie].services.push(this.services[i]);
                    }
                    this.categories = categories;
                    console.log(categories)
                });
            },
            loadAppointment() {
                this.$http.get('/wp-json/krack/v1/appointment').then((response) => {
                    this.appointments = response.body;
                    this.workingDay = this.workingDay.slice(0);
                    this.loadWeek();
                });
            },
            loadWorkingDay() {
                this.$http.get('/wp-json/krack/v1/workingDay').then((response) => {
                    var self = this;
                    this.workingDay = response.body;
                    this.workingDay.forEach(function (day) {
                        day.period.forEach(function (period) {
                            if (period.startHour <= self.firstHourWeek) {
                                self.firstHourWeek = period.startHour;
                                if (period.startMinute <= self.firstMinWeek) {
                                    self.firstMinWeek = period.startMinute;
                                }
                            }

                            if (period.endHour >= self.lastHourWeek) {
                                self.lastHourWeek = period.endHour;
                                if (period.endMinute > self.lastMinWeek) {
                                    self.lastMinWeek = period.endMinute;
                                }
                            }
                        });
                    });
                    this.loadWeek();
                });
            },
            order: function (service) {
                this.view = 'calendar';
                this.selectedService = service;
                this.loadWorkingDay();
                this.loadAppointment();

            },
            cancelOrder: function () {
                this.view = 'services';
                this.selectedService = null;
            },
            isAppointmentHour: function (day, hour, checkFree) {
                var busy = false;
                var cleanDate = function (date) {
                    date.setSeconds(0);

                    return new Date(date.getYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), 0, 0);

                }

                this.appointments.forEach(function (appointment) {
                    //we check only appointement with same appatment type (free or busy)
                    if (appointment.free != checkFree) {
                        return;
                    }

                    var appointmentStartDate = new Date(appointment.start);
                    appointmentStartDate = cleanDate(appointmentStartDate);
                    var appointmentEndDate = new Date(appointment.end);
                    appointmentEndDate = cleanDate(appointmentEndDate);

                    var hourStart = new Date(day.date);
                    hourStart.setHours(hour.hour);
                    hourStart.setMinutes(hour.min);
                    hourStart = cleanDate(hourStart);
                    var hourEnd = new Date(day.date);
                    hourEnd.setHours(hour.hour);
                    hourEnd.setMinutes((hour.min + 15));
                    hourEnd = cleanDate(hourEnd);

                    if (appointmentStartDate <= hourStart && appointmentEndDate >= hourEnd) {


                        busy = true;
                    }
                });

                return busy;
            },
            isSoCloseHour: function (day, hour, during) {
                var soClose = false;
                var cleanDate = function (date) {
                    date.setSeconds(0);

                    return new Date(date.getYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), 0, 0);

                }

                this.appointments.forEach(function (appointment) {
                    if (appointment.free) {
                        return;
                    }
                    var appointmentStartDate = new Date(appointment.start);
                    appointmentStartDate = cleanDate(appointmentStartDate);

                    var firstPossibility = new Date(appointment.start);
                    firstPossibility.setMinutes(firstPossibility.getMinutes() - during);
                    firstPossibility = cleanDate(firstPossibility);

                    var hourStart = new Date(day.date);
                    hourStart.setHours(hour.hour);
                    hourStart.setMinutes(hour.min);
                    hourStart = cleanDate(hourStart);
                    var hourEnd = new Date(day.date);
                    hourEnd.setHours(hour.hour);
                    hourEnd.setMinutes((hour.min + 15));
                    hourEnd = cleanDate(hourEnd);


                    if (hourStart > firstPossibility && hourStart <= appointmentStartDate) {
                        soClose = true;
                    }
                });

                return soClose;
            },
            isWorkedHour: function (day, hour) {
                var found = false;
                day.period.forEach(function (period) {
                    var checkStart = function () {
                        if (period.startHour < hour.hour) {
                            return true;
                        } else if (period.startHour == hour.hour) {
                            if (period.startMinute <= hour.min) {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    };

                    var checkEnd = function () {
                        if (period.endHour > hour.hour) {
                            return true;
                        } else if (period.endHour == hour.hour) {
                            if (period.endMinute > hour.min) {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    };
                    if (checkStart() && checkEnd()) {
                        found = true;
                    }
                });
                return found;
            },
            isPast: function (day, hour) {
                var dateToCheck = new Date(day.date);
                dateToCheck.setHours(hour.hour);
                dateToCheck.setMinutes(hour.min);
                return dateToCheck < new Date();
            },
            getWorkedHours: function () {
                var hours = [];
                var diffMinutes = 0;
                if (this.lastMinWeek > 0) {
                    diffMinutes = 1;
                }
                for (var i = this.firstHourWeek; i < this.lastHourWeek + diffMinutes; i++) {
                    for (var j = 0; j < 4; j++) {
                        var hour = {
                            hour: i,
                            min: j * 15
                        }

                        hours.push(hour);
                    }
                }
                return hours;
            },
            getHoursStatus: function (day, hour) {
                var status = "";
                if (this.isWorkedHour(day, hour)) {
                    status += "worked "
                }
                if (this.isPast(day, hour)) {
                    status += "past "
                }
                if (this.isAppointmentHour(day, hour, true)) {
                    status += "exOpen "
                }

                if (this.isAppointmentHour(day, hour, false)) {
                    status += "busy "
                }

                if (this.isSoCloseHour(day, hour, this.selectedService.during)) {
                    status += "close "
                }
                return status;
            },
            select(day, hour, during) {
                console.log("isWorkedHour " + this.isWorkedHour(day, hour));
                console.log("isAppointmentHour " + this.isAppointmentHour(day, hour, true));
                if ((this.isWorkedHour(day, hour) || this.isAppointmentHour(day, hour, true)) && !this.isPast(day, hour) && !this.isAppointmentHour(day, hour, false) && !this.isSoCloseHour(day, hour, during)) {
                    this.contact = true;
                    var date = new Date(day.date);
                    date.setHours(hour.hour);
                    date.setMinutes(hour.min);
                    this.selectedStartDate = date;
                }
            },
            checkForm(e) {
                e.preventDefault();
                this.confirm();
            },
            confirm() {
                var appointment = {
                    'type': this.selectedService.id,
                    'date': this.selectedStartDate,
                    'name': this.name,
                    'phone': this.phone
                };

                this.$http.post('/wp-json/krack/v1/appointment', appointment).then((response) => {
                    this.appointments = response.body;
                    this.selectedStartDate = null;
                    this.selectedService = null;
                    alert("Rendez vous confirmé");
                    this.contact = false;

                    this.view = 'services'
                });
            },
            loadWeek() {
                var now = new Date();
                now.setDate(now.getDate() + 7 * this.weekDiff);
                var i = 1;
                this.workingDay.forEach(function (day) {
                    var dayDate = new Date(now);
                    var dayInTheWeek = dayDate.getDay();
                    if (dayInTheWeek === 0) {
                        dayInTheWeek = 7;
                    }
                    var dayDiff = dayInTheWeek - i;
                    dayDate.setDate(dayDate.getDate() - dayDiff);
                    day.date = dayDate;
                    i++;
                });
            },
            resetWeek() {
                this.weekDiff = 0;
                this.loadWeek();
                this.workingDay = this.workingDay.slice(0);
            },
            changeWeek(diff) {
                this.weekDiff = this.weekDiff + diff;
                this.loadWeek();
                this.workingDay = this.workingDay.slice(0);
            }
        }
    })
}