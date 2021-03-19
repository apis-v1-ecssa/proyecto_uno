<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-data-table
        :headers="dessertHeaders"
        :items="desserts"
        :expanded.sync="expanded"
        :single-expand="singleExpand"
        item-key="docto_no"
        show-expand
        class="elevation-1"
        :search="search"
        :no-data-text="'No hay facturaciones esperando para firma'"
        :no-results-text="'El registro que usted busca no se encuentra'"
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-toolbar-title>Entregas pendientes de firma</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              clearable
              flat
              solo-inverted
              hide-details
              prepend-inner-icon="mdi-magnify"
              label="Buscar"
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn class="mr-1" color="success" @click="initialize"
              >Recargar</v-btn
            >
          </v-toolbar>
        </template>
        <template v-slot:item.status="{ item }">
          <v-tooltip left>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                color="success"
                dark
                v-bind="attrs"
                v-on="on"
                @click="recibir(item)"
              >
                Aceptar de Recibido
              </v-btn>
            </template>
            <span>El cliente firma de recibido.</span>
          </v-tooltip>
        </template>
        <template v-slot:expanded-item="{ headers, item }">
          <td :colspan="headers.length">
            <br />
            <v-simple-table dense fixed-header light>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-center">Código</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Producto</th>
                    <th class="text-center">Observación</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(producto, index) in item.detail" :key="index">
                    <td>{{ producto.item_code }}</td>
                    <td>{{ producto.amount }}</td>
                    <td>{{ producto.description }}</td>
                    <td>{{ producto.observation }}</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <br />
          </td>
        </template>
      </v-data-table>
    </v-col>

    <v-dialog
      v-model="dialog"
      eager
      fullscreen
      hide-overlay
      persistent
      transition="dialog-bottom-transition"
    >
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-toolbar dark color="success">
          <v-btn icon dark @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-toolbar-title>Firmar de Recibido</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="error"
                  dark
                  v-bind="attrs"
                  v-on="on"
                  @click="limpiar"
                >
                  Limpiar
                </v-btn>
              </template>
              <span>Limpiar para agregar otra firma.</span>
            </v-tooltip>
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  dark
                  v-bind="attrs"
                  v-on="on"
                  @click="firmar()"
                >
                  Firmar
                </v-btn>
              </template>
              <span
                >El cliente acepta de conformidad los productos facturados y
                entregados.</span
              >
            </v-tooltip>
          </v-toolbar-items>
        </v-toolbar>
        <canvas
          id="draw-canvas"
          ref="canvas_img"
          @mousedown="startPainting"
          @mouseup="finishedPainting"
          @mousemove="draw"
          @touchstart="startTouch"
          @touchend="finishedTouch"
          @touchleave="leaveTouch"
          @touchmove="moveTouch"
        >
          No tienes un buen navegador.
        </canvas>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<style scoped>
#draw-canvas {
  border: 3px solid black;
  border-radius: 5px;
  cursor: crosshair;
  background: white;
  position: fixed;
  left: 10;
  top: 20;
  right: 10;
  bottom: 20;
  width: 100%;
  height: 100%;
}
</style>

<script>
export default {
  name: "Completo",
  data() {
    return {
      loading: false,
      dialog: false,
      search: "",
      expanded: [],
      singleExpand: false,
      dessertHeaders: [
        {
          text: "Documento",
          align: "start",
          value: "docto_no",
        },
        { text: "Cliente", value: "client.card_code" },
        { text: "Asignado", value: "user.full_name" },
        { text: "Vendedor", value: "seller" },
        { text: "Facturado", value: "doc_date" },
        { text: "Completado", value: "delivery_time" },
        { text: "Detalle", value: "data-table-expand", sortable: false },
        { text: "Estado", value: "status", sortable: false },
      ],
      form: {
        id: 0,
        firma: null,
      },
      desserts: [],
      painting: false,
      canvas: null,
      ctx: null,
      mousePos: { x: 0, y: 0 },
      lastPos: null,
    };
  },

  mounted() {
    this.canvas = this.$refs.canvas_img;
    this.ctx = this.canvas.getContext("2d");
    this.canvas.height = window.innerHeight;
    this.canvas.width = window.innerWidth;
  },

  created() {
    this.initialize();
  },

  methods: {
    initialize() {
      this.loading = true;
      this.lastPos = this.mousePos;

      this.$store.state.services.ventaService
        .completo()
        .then((r) => {
          if (r.response) {
            if (r.response.data.code === 423) {
              this.$toastr.error(r.response.data.error, "Mensaje");
            } else {
              for (let value of Object.values(r.response.data.error)) {
                this.$toastr.error(value, "Mensaje");
              }
            }
            this.loading = false;
            return;
          }

          this.desserts = r.data;
          this.dialog = false;
          this.loading = false;
        })
        .catch((r) => {
          this.loading = false;
        });
    },

    recibir(item) {
      this.form.id = item.id;
      this.form.firma = null;
      this.canvas.width = this.canvas.width;
      this.dialog = true;
    },

    firmar() {
      this.$swal({
        title: "Firmar y Aceptar",
        text: "¿Está seguro de realizar esta acción?",
        type: "success",
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          let data = new Object();
          data.id = this.form.id;
          data.firma = this.canvas.toDataURL();

          this.loading = true;
          this.$store.state.services.ventaService
            .aceptar(data)
            .then((r) => {
              this.loading = false;

              if (r.response) {
                if (r.response.data.code === 404) {
                  this.$toastr.warning(r.response.data.error, "Advertencia");
                  return;
                } else if (r.response.data.code === 423) {
                  this.$toastr.warning(r.response.data.error, "Advertencia");
                  return;
                } else {
                  for (let value of Object.values(r.response.data)) {
                    this.$toastr.error(value, "Mensaje");
                  }
                }
                return;
              }

              this.$toastr.success(r.data, "Mensaje");
              this.initialize();
            })
            .catch((r) => {
              this.loading = false;
            });
        }
      });
    },

    //Funciones para la firma
    startPainting(event) {
      this.painting = true;
      this.lastPos = this.getMousePos(this.canvas, event);
    },
    finishedPainting() {
      this.painting = false;
    },
    draw(event) {
      this.mousePos = this.getMousePos(this.canvas, event);
      if (this.painting) {
        this.ctx.strokeStyle = "#000000";
        this.ctx.beginPath();
        this.ctx.moveTo(this.lastPos.x, this.lastPos.y);
        this.ctx.lineTo(this.mousePos.x, this.mousePos.y);
        this.ctx.lineWidth = 11;
        this.ctx.lineCap = "round";
        this.ctx.stroke();
        this.ctx.closePath();
        this.lastPos = this.mousePos;
      }
    },
    limpiar() {
      this.canvas.width = this.canvas.width;
    },

    //Otras funciones
    getMousePos(canvasDom, mouseEvent) {
      var rect = canvasDom.getBoundingClientRect();

      return {
        x: mouseEvent.clientX - rect.left,
        y: mouseEvent.clientY - rect.top,
      };
    },

    getTouchPos(canvasDom, touchEvent) {
      var rect = canvasDom.getBoundingClientRect();

      return {
        x: touchEvent.touches[0].clientX - rect.left,
        y: touchEvent.touches[0].clientY - rect.top,
      };
    },

    startTouch(event) {
      this.mousePos = this.getTouchPos(this.canvas, event);
      event.preventDefault();
      var touch = event.touches[0];
      var mouseEvent = new MouseEvent("mousedown", {
        clientX: touch.clientX,
        clientY: touch.clientY,
      });
      this.canvas.dispatchEvent(mouseEvent);
    },

    finishedTouch(event) {
      event.preventDefault();
      var mouseEvent = new MouseEvent("mouseup", {});
      this.canvas.dispatchEvent(mouseEvent);
    },

    leaveTouch(event) {
      event.preventDefault(); // Prevent scrolling when touching the canvas
      var mouseEvent = new MouseEvent("mouseup", {});
      this.canvas.dispatchEvent(mouseEvent);
    },

    moveTouch(event) {
      event.preventDefault(); // Prevent scrolling when touching the canvas
      var touch = event.touches[0];
      var mouseEvent = new MouseEvent("mousemove", {
        clientX: touch.clientX,
        clientY: touch.clientY,
      });
      this.canvas.dispatchEvent(mouseEvent);
    },
  },
};
</script>