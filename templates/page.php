<?php get_header(); ?>


<div id="app">
    <div id="services" v-if="view === 'services'">
        <div v-for="categorie in categories"  class="categorie">
            <h1>{{ categorie.name }}</h1>
            <div v-for="service in categorie.services" class="service">
                <h2>{{ service.name }}</h2>
                <p>{{ service.describe }}</p>
                <span class="price">{{ service.price }}</span>
                <span class="time">{{ service.during }}</span>
                <button v-on:click="order(service)" title="Réserver en ligne">Réserver</button>
            </div>
        </div>
    </div>
    <div id="calendar" v-if="view === 'calendar'">

        <button v-on:click="cancelOrder()">retour</button>
        <h2>{{selectedService.name}}</h2>
        <p>{{selectedService.describe}}</p>
        <span class="price">{{selectedService.price}}</span>
        <span class="time">{{selectedService.during }}</span>


        <div id="planning">
            <div class="nav">
                <button v-on:click="changeWeek(-1)" class="previous">previous</button>
                <button v-on:click="resetWeek()" class="now" >Aujourd'hui</button>
                <button v-on:click="changeWeek(+1)" class="next">next</button>
            </div>
            <table>
                <tr>
                    <th></th>
                    <th class="day" v-bind:class="{ past: day.isPast }" v-for="day in workingDay">
                        {{day.day}} / {{day.date.getDate()}}/{{day.date.getMonth()+1}}  
                    </th>
                </tr>
                <tr v-for="hour in getWorkedHours()">
                    <td>{{hour.hour}}h{{hour.min}}</td>
                    <td class="hour" v-for="day in workingDay"  v-bind:title="hour.hour+'h'+hour.min" v-bind:class="getHoursStatus(day, hour)" v-on:click="select(day, hour, selectedService.during)">

                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div id="contact" v-if="contact">
        <h2>Confirmation</h2>
        <form v-on:submit.prevent="checkForm">

        <p>
            <label for="name">Nom</label>
            <input
            id="name"
            v-model="name"
            type="text"
            name="name"
            required
            >
        </p>

        <p>
            <label for="age">Téléphone</label>
            <input
            id="phone"
            v-model="phone"
            type="phone"
            name="phone"
            required>
        </p>

        <p>
            <input
            type="submit"
            value="Confirmer"
            >
        </p>

        </form>
    </div>

</div>
<?php get_footer(); ?>
<!-- development version, includes helpful console warnings -->
