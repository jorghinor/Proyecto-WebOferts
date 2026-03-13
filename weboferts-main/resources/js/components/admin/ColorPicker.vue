 <template>
 <div class="text-left">
    <v-menu 
      v-model="menu"
      :close-on-content-click="false"
      :nudge-width="200"
      offset-x
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn class="py-3" dark color="blue" fab v-bind="attrs" v-on="on">
          <v-icon>mdi-palette</v-icon>
        </v-btn>
      </template>

      <v-card persistent fullscreen max-width="300px">
       <v-card-text>
         <v-row
            justify="space-around"
            align="center"
         >
         <v-color-picker
            mode="hexa"
            v-model="picker"
            flat
         ></v-color-picker>
         </v-row>
       </v-card-text>
        <v-card-actions>
          <v-btn
            text
            @click="menu = false"
          >
            Cerrar
          </v-btn>
          <v-btn class="success"
            text
            @click="enviar"
          >
            Ok
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>
  </div>
</template>
<script>
export default {
  name: "ColorPicker",  
  //props: {
    //micolor: String,
  //},  
    data: () => ({
      fav: true,
      menu: false,
      message: false,
      hints: true,

      picker: null,
      row: null,
    }),
   methods: {
    enviar(){
      this.$emit("cambiar", this.picker.hex)  
    }
    },
}
</script>