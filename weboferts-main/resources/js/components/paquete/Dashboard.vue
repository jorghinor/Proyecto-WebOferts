<template>
  <v-app id="inspire">
    <navbarpaquete :pageTitle="'Dashboard'"></navbarpaquete>

    <v-main>
      <v-row>
        <v-col>
          <v-sparkline
            :value="value"
            :gradient="gradient"
            :smooth="radius || false"
            :padding="padding"
            :line-width="width"
            :stroke-linecap="lineCap"
            :gradient-direction="gradientDirection"
            :fill="fill"
            :type="type"
            :auto-line-width="autoLineWidth"
            auto-draw
          ></v-sparkline>
        </v-col>
      </v-row>
    </v-main>

    <footerpaquete></footerpaquete>
  </v-app>
</template>

<script>
const gradients = [
  ["#222"],
  ["#42b3f4"],
  ["red", "orange", "yellow"],
  ["purple", "violet"],
  ["#00c6ff", "#F0F", "#FF0"],
  ["#f72047", "#ffd200", "#1feaea"],
];

export default {
  name: "Dashboard",
  data: () => ({
    width: 2,
    radius: 10,
    padding: 8,
    lineCap: "round",
    gradient: gradients[5],
    value: [0, 2, 5, 9, 5, 10, 3, 5, 0, 0, 1, 8, 2, 9, 0],
    gradientDirection: "top",
    gradients,
    fill: false,
    type: "trend",
    autoLineWidth: false,
  }),
};
</script>
<style scoped>
/*   .theme--light.v-btn.v-btn--has-bg.active {
      background-color: #cec819;
  } */

.topnav a.active {
  border-bottom: 3px solid rgb(252, 252, 252);
}
</style>