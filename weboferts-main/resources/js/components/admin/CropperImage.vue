<template>
  <v-dialog persistent v-model="dialog" width="605">
    <template v-slot:activator="{ on }">
      <v-btn small color="success" v-on="on">
        Cambiar
        <v-icon dark right>mdi-camera</v-icon>
      </v-btn>
    </template>

    <v-card class="px-2" dark color="blackblue">
      <v-card-title class="py-0"> Cambiar Imagen </v-card-title>

      <v-card height="600" width="600" color="white">
        <vue-cropper
          ref="cropper"
          :img="option.img"
          :output-size="option.size"
          :output-type="option.outputType"
          :info="true"
          :full="option.full"
          :fixed="fixed"
          :fixed-number="fixedNumber"
          :can-move="option.canMove"
          :can-move-box="option.canMoveBox"
          :fixed-box="option.fixedBox"
          :original="option.original"
          :auto-crop="option.autoCrop"
          :auto-crop-width="option.autoCropWidth"
          :auto-crop-height="option.autoCropHeight"
          :center-box="option.centerBox"
          @real-time="realTime"
          :high="option.high"
          @img-load="imgLoad"
          mode="cover"
        ></vue-cropper>
      </v-card>
      <input
        ref="input"
        type="file"
        id="uploads"
        accept="image/png, image/jpeg, image/gif, image/jpg"
        @change="uploadImg($event, 1)"
      />
      <v-btn small @click.prevent="showFileChooser" class="success black--text">
        <v-icon small>mdi-camara</v-icon>Imagen
      </v-btn>
      <v-btn small @click="refreshCrop">
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
      <v-btn small @click="changeScale(3)">
        <v-icon>mdi-magnify-plus</v-icon>
      </v-btn>
      <v-btn small @click="changeScale(-3)">
        <v-icon>mdi-magnify-minus</v-icon>
      </v-btn>
      <v-btn small @click="rotateLeft">
        <v-icon>mdi-rotate-left</v-icon>
      </v-btn>
      <v-btn small @click="rotateRight">
        <v-icon>mdi-rotate-right</v-icon>
      </v-btn>

      <v-card-actions>
        <v-btn
          small
          rounded
          :disabled="!valid"
          @click="submit"
          class="skyblue blackblue--text"
          :loading="loading"
        >
          Guardar
        </v-btn>
        <v-btn small text rounded class="skyblue--text" @click="close">
          Cancelar
        </v-btn>
      </v-card-actions>

      <v-snackbar v-model="verMensaje" color="success" :timeout="3000" top>{{
        imageResultMesage
      }}</v-snackbar>
    </v-card>
  </v-dialog>
</template>

<script>
import { VueCropper } from "vue-cropper";

export default {
  name: "EditarImagen",
  props: {
    //userImage: String
  },
  components: {
    VueCropper,
  },
  data() {
    return {
      model: false,
      modelSrc: "",
      crap: false,
      previews: {},
      option: {
        //img: 'https://source.unsplash.com/random/300×300',
        img: "",
        size: 2,
        full: false,
        outputType: "jpeg",
        canMove: true,
        fixedBox: false,
        original: true,
        canMoveBox: true,
        autoCrop: true,

        autoCropWidth: 600,
        autoCropHeight: 600,
        centerBox: false,
        high: true,
      },
      show: true,
      fixed: true,
      fixedNumber: [1, 1],

      verMensaje: false,
      imageResultMesage: "",
      valid: false,
      loading: false,
      dialog: false,
      verFotos: false,
    };
  },

  methods: {
    showFileChooser() {
      this.$refs.input.click();
    },
    startCrop() {
      // start
      this.crap = true;
      this.$refs.cropper.startCrop();
    },
    stopCrop() {
      //  stop
      this.crap = false;
      this.$refs.cropper.stopCrop();
    },
    clearCrop() {
      // clear
      this.$refs.cropper.clearCrop();
    },
    refreshCrop() {
      // clear
      this.$refs.cropper.refresh();
    },
    changeScale(num) {
      num = num || 1;
      this.$refs.cropper.changeScale(num);
    },
    rotateLeft() {
      this.$refs.cropper.rotateLeft();
    },
    rotateRight() {
      this.$refs.cropper.rotateRight();
    },
    finish(type) {
      // var test = window.open('about:blank')
      // test.document.body.innerHTML = '图片生成中..'
      if (type === "blob") {
        this.$refs.cropper.getCropBlob((data) => {
          var img = window.URL.createObjectURL(data);
          this.model = true;
          this.modelSrc = img;
        });
      } else {
        this.$refs.cropper.getCropData((data) => {
          this.model = true;
          this.modelSrc = data;
        });
      }
    },

    realTime(data) {
      this.previews = data;
      //console.log(data)
    },

    finish2(type) {
      this.$refs.cropper2.getCropData((data) => {
        this.model = true;
        this.modelSrc = data;
      });
    },
    finish3(type) {
      this.$refs.cropper3.getCropData((data) => {
        this.model = true;
        this.modelSrc = data;
      });
    },
    down(type) {
      // event.preventDefault()
      var aLink = document.createElement("a");
      aLink.download = "demo";

      if (type === "blob") {
        this.$refs.cropper.getCropBlob((data) => {
          this.downImg = window.URL.createObjectURL(data);
          aLink.href = window.URL.createObjectURL(data);
          aLink.click();
        });
      } else {
        this.$refs.cropper.getCropData((data) => {
          this.downImg = data;
          aLink.href = data;
          aLink.click();
        });
      }
    },

    uploadImg(e, num) {
      // this.option.img
      var file = e.target.files[0];
      if (!/\.(gif|jpg|jpeg|png|bmp|GIF|JPG|PNG)$/.test(e.target.value)) {
        alert("Solo archivos .gif,jpeg,jpg,png,bmp son permitidos");
        return false;
      }
      var reader = new FileReader();
      reader.onload = (e) => {
        let data;
        if (typeof e.target.result === "object") {
          data = window.URL.createObjectURL(new Blob([e.target.result]));
        } else {
          data = e.target.result;
        }
        if (num === 1) {
          this.option.img = data;
        } else if (num === 2) {
          this.example2.img = data;
        }
      };
      //base64
      // reader.readAsDataURL(file)
      //blob
      this.valid = true;
      reader.readAsArrayBuffer(file);
    },
    imgLoad(msg) {
      //console.log(msg)
    },
    submit() {
      this.loading = true;
      this.$refs.cropper.getCropBlob((data) => {
        const formData = new FormData();
        formData.append("imagen", data);
        formData.append("type", "jpg");
        axios
          .post(window.location.origin + "/admin/files", formData, {
            headers: { "Content-Type": "multipart/form-data" },
          })
          .then((response) => {
            //setTimeout(this.createNegocio(response.data.imageName), 3000);
            this.loading = false;
            this.dialog = false;
            this.$emit("imagenChanged", response.data.imageName);
          })
          .catch((err) => {
            console.error(err);
          });
      });

      //canvas.toBlob((blob) => {

      //})
    },

    close() {
      this.option.img = "";
      this.dialog = false;
    },
  },
  mounted: function () {
    //this.option.img = this.userImage
  },
};
</script>

<style scoped>
input[type="file"] {
  display: none;
}

.cut {
  width: 500px;
  height: 500px;
  margin: 30px auto;
}

.c-item {
  max-width: 800px;
  margin: 10px auto;
  margin-top: 20px;
}

.content {
  margin: auto;
  max-width: 1200px;
  margin-bottom: 100px;
}

.test-v-btn {
  display: flex;
  flex-wrap: wrap;
  align-content: center;
  justify-content: center;
}

.des {
  line-height: 30px;
}

code.language-html {
  padding: 10px 20px;
  margin: 10px 0px;
  display: block;
  background-color: #333;
  color: #fff;
  overflow-x: auto;
  font-family: Consolas, Monaco, Droid, Sans, Mono, Source, Code, Pro, Menlo,
    Lucida, Sans, Type, Writer, Ubuntu, Mono;
  border-radius: 5px;
  white-space: pre;
}

.show-info {
  margin-bottom: 50px;
}

.show-info h2 {
  line-height: 50px;
}

/*.title, .title:hover, .title-focus, .title:visited {
        color: black;
      }*/

.test {
  height: 500px;
}

.model {
  position: fixed;
  z-index: 10;
  width: 100vw;
  height: 100vh;
  overflow: auto;
  top: 0;
  left: 0;
  background: rgba(0, 0, 0, 0.8);
}

.model-show {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100vw;
  height: 100vh;
}

.model img {
  display: block;
  margin: auto;
  max-width: 80%;
  user-select: none;
  background-position: 0px 0px, 10px 10px;
  background-size: 20px 20px;
  background-image: linear-gradient(
      45deg,
      #eee 25%,
      transparent 25%,
      transparent 75%,
      #eee 75%,
      #eee 100%
    ),
    linear-gradient(45deg, #eee 25%, white 25%, white 75%, #eee 75%, #eee 100%);
}

.c-item {
  display: block;
  user-select: none;
}

@keyframes slide {
  0% {
    background-position: 0 0;
  }
  100% {
    background-position: -100% 0;
  }
}
</style>

