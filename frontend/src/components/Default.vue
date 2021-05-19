<template>
  <v-row class="fill-height">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-container fluid>
      <v-data-iterator
        :items="items"
        :items-per-page.sync="itemsPerPage"
        hide-default-footer
        :search="search"
        :no-data-text="'No hay facturaciones para entregar'"
        :no-results-text="'El registro que usted busca no se encuentra'"
      >
        <template v-slot:header>
          <v-toolbar class="mb-2" color="indigo darken-5" dark flat>
            <v-toolbar-title>Ordenes facturadas</v-toolbar-title>
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
            <v-btn class="mr-1" color="success" @click="facturados">
              Recargar
            </v-btn>
          </v-toolbar>
        </template>

        <template v-slot:default="props">
          <v-row dense>
            <v-col
              v-for="item in props.items"
              :key="item.docto_no"
              cols="12"
              md="6"
            >
              <v-card class="mx-auto" :color="item.status.color" dark>
                <v-card-title class="subheading font-weight-bold">
                  Documento No. {{ item.docto_no }}
                </v-card-title>
                <v-chip class="ma-2" color="indigo" text-color="white">
                  <v-avatar left>
                    <v-icon>mdi-checkbox-marked-circle</v-icon>
                  </v-avatar>
                  Vendedor - {{ item.seller }}
                </v-chip>
                <v-chip class="ma-2" color="orange" text-color="white">
                  {{ item.series }}
                  <v-icon right>mdi-star</v-icon>
                </v-chip>
                <v-chip class="ma-2" color="success" text-color="white">
                  {{ item.doc_date }}
                  <v-icon right>mdi-server-plus</v-icon>
                </v-chip>
                <v-chip class="ma-2" color="teal" text-color="white">
                  <v-avatar left>
                    <v-icon>mdi-account-circle</v-icon>
                  </v-avatar>
                  Asignado - {{ item.user.full_name }}
                </v-chip>

                <v-divider></v-divider>

                <v-list
                  transparent
                  v-for="producto in item.detail"
                  v-bind:key="producto.id"
                >
                  <v-list-item>
                    <v-list-item-content>
                      <v-list-item-title>
                        <h3>{{ producto.item_code }}</h3>
                      </v-list-item-title>
                      {{ producto.amount }} | {{ producto.description }}
                    </v-list-item-content>
                    <v-list-item-content class="align-end">
                      <vue-number-input
                        v-model="producto.found"
                        :min="0"
                        :max="producto.amount"
                        controls
                        size="large"
                        :step="1"
                        :disabled="producto.found == producto.amount"
                      ></vue-number-input>
                      <v-textarea
                        v-model="producto.observation"
                        label="Observación"
                        counter
                        maxlength="200"
                        full-width
                        single-line
                      ></v-textarea>
                    </v-list-item-content>
                  </v-list-item>
                </v-list>

                <v-card-actions v-if="item.status.name != 'COMPLETO'">
                  <div
                    class="text-center d-flex align-center justify-space-around"
                  >
                    <v-btn-toggle v-model="toggle_exclusive" mandatory>
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            color="primary"
                            dark
                            v-bind="attrs"
                            v-on="on"
                            @click="guardar(item)"
                          >
                            Guardar avance
                          </v-btn>
                        </template>
                        <span>
                          Completar uno a uno los productos hasta que el sistema
                          detecta que los productos están completos.
                        </span>
                      </v-tooltip>
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            color="success"
                            dark
                            v-bind="attrs"
                            v-on="on"
                            @click="entregar(item)"
                          >
                            Entrega completo
                          </v-btn>
                        </template>
                        <span>
                          Completar la orden para la entrega, no es necesario
                          ingresar los valores.
                        </span>
                      </v-tooltip>
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            color="error"
                            dark
                            v-bind="attrs"
                            v-on="on"
                            @click="cancelar(item)"
                          >
                            Cancelar
                          </v-btn>
                        </template>
                        <span>Cancelar entrega.</span>
                      </v-tooltip>
                    </v-btn-toggle>
                  </div>
                </v-card-actions>

                <v-card-actions v-else>
                  <v-tooltip bottom>
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
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </template>
      </v-data-iterator>

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
                <span>
                  El cliente acepta de conformidad los productos facturados y
                  entregados.
                </span>
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
    </v-container>
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
  name: 'Default',
  components: {},
  data() {
    return {
      loading: false,
      dialog: false,
      itemsPerPage: 100,
      search: '',
      items: [],
      form: {
        id: 0,
        firma: null,
      },
      painting: false,
      canvas: null,
      ctx: null,
      mousePos: { x: 0, y: 0 },
      lastPos: null,
      tint: null,
    }
  },

  mounted() {
    this.canvas = this.$refs.canvas_img
    this.ctx = this.canvas.getContext('2d')
    this.canvas.height = window.innerHeight
    this.canvas.width = window.innerWidth
  },

  created() {
    this.facturados()
  },

  methods: {
    facturados() {
      this.loading = true
      this.lastPos = this.mousePos

      this.$store.state.services.ventaService
        .facturado()
        .then((r) => {
          if (r.response) {
            if (r.response.data.code === 423) {
              this.$toastr.error(r.response.data.error, 'Mensaje')
            } else {
              for (let value of Object.values(r.response.data.error)) {
                this.$toastr.error(value, 'Mensaje')
              }
            }
            this.loading = false
            return
          }

          this.items = r.data
          this.dialog = false
          this.loading = false
        })
        .catch((r) => {
          this.loading = false
        })
    },

    entregar(item) {
      let descrip = null
      this.$swal({
        title: 'Agregar una descripción',
        input: 'textarea',
        inputPlaceholder: 'Agregar descripción...',
        inputAttributes: {
          'aria-label': 'Type your message here',
        },
        showCancelButton: false,
        confirmButtonText: 'Agregar',
        preConfirm: (descripcion) => {
          descrip = descripcion
        },
      }).then((result) => {
        this.$swal({
          title: 'Entregar Producto Completo',
          text: '¿Está seguro de realizar esta acción?',
          type: 'success',
          showCancelButton: true,
        }).then((result) => {
          if (result.value) {
            let data = new Object()
            data.id = item.id
            data.description = descrip

            this.loading = true
            this.$store.state.services.ventaService
              .completar_todo(data)
              .then((r) => {
                this.loading = false

                if (r.response) {
                  if (r.response.data.code === 404) {
                    this.$toastr.warning(r.response.data.error, 'Advertencia')
                    return
                  } else if (r.response.data.code === 423) {
                    this.$toastr.warning(r.response.data.error, 'Advertencia')
                    return
                  } else {
                    for (let value of Object.values(r.response.data)) {
                      this.$toastr.error(value, 'Mensaje')
                    }
                  }
                  return
                }

                this.$toastr.success(r.data, 'Mensaje')
                this.facturados()
              })
              .catch((r) => {
                this.loading = false
              })
          }
        })
      })
    },

    guardar(item) {
      this.$swal({
        title: 'Entregar Producto',
        text: '¿Está seguro de realizar esta acción?',
        type: 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          let data = new Object()
          data.id = item.id
          data.detail = item.detail

          this.loading = true
          this.$store.state.services.ventaService
            .entregar(data)
            .then((r) => {
              this.loading = false

              if (r.response) {
                if (r.response.data.code === 404) {
                  this.$toastr.warning(r.response.data.error, 'Advertencia')
                  return
                } else if (r.response.data.code === 423) {
                  this.$toastr.warning(r.response.data.error, 'Advertencia')
                  return
                } else {
                  for (let value of Object.values(r.response.data)) {
                    this.$toastr.error(value, 'Mensaje')
                  }
                }
                return
              }

              this.$toastr.success(r.data, 'Mensaje')
              this.facturados()
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },

    cancelar(item) {
      this.$swal({
        title: 'Cancelar Entrega',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          let data = new Object()
          data.id = item.id

          this.loading = true
          this.$store.state.services.ventaService
            .cancelar(data)
            .then((r) => {
              this.loading = false

              if (r.response) {
                if (r.response.data.code === 404) {
                  this.$toastr.warning(r.response.data.error, 'Advertencia')
                  return
                } else if (r.response.data.code === 423) {
                  this.$toastr.warning(r.response.data.error, 'Advertencia')
                  return
                } else {
                  for (let value of Object.values(r.response.data)) {
                    this.$toastr.error(value, 'Mensaje')
                  }
                }
                return
              }

              this.$toastr.success(r.data, 'Mensaje')
              this.facturados()
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },

    recibir(item) {
      this.form.id = item.id
      this.form.firma = null
      this.canvas.width = this.canvas.width
      this.dialog = true
    },

    firmar() {
      this.$swal({
        title: 'Firmar y Aceptar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          let data = new Object()
          data.id = this.form.id
          data.firma = this.canvas.toDataURL()

          this.loading = true
          this.$store.state.services.ventaService
            .aceptar(data)
            .then((r) => {
              this.loading = false

              if (r.response) {
                if (r.response.data.code === 404) {
                  this.$toastr.warning(r.response.data.error, 'Advertencia')
                  return
                } else if (r.response.data.code === 423) {
                  this.$toastr.warning(r.response.data.error, 'Advertencia')
                  return
                } else {
                  for (let value of Object.values(r.response.data)) {
                    this.$toastr.error(value, 'Mensaje')
                  }
                }
                return
              }

              this.$toastr.success(r.data, 'Mensaje')
              this.facturados()
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },

    //Funciones para la firma
    startPainting(event) {
      this.painting = true
      this.lastPos = this.getMousePos(this.canvas, event)
      //this.draw(event);
    },
    finishedPainting() {
      this.painting = false
      //this.ctx.beginPath();
    },
    draw(event) {
      this.mousePos = this.getMousePos(this.canvas, event)
      if (this.painting) {
        this.ctx.strokeStyle = '#000000'
        this.ctx.beginPath()
        this.ctx.moveTo(this.lastPos.x, this.lastPos.y)
        this.ctx.lineTo(this.mousePos.x, this.mousePos.y)
        this.ctx.lineWidth = 11
        this.ctx.lineCap = 'round'
        this.ctx.stroke()
        this.ctx.closePath()
        this.lastPos = this.mousePos
      }
      /*if (!this.painting) return;

      this.ctx.lineWidth = 12;
      this.ctx.lineCap = "round";

      this.ctx.lineTo(event.clientX, event.clientY);
      this.ctx.stroke();

      this.ctx.beginPath();
      this.ctx.moveTo(event.clientX, event.clientY);*/
    },
    limpiar() {
      this.canvas.width = this.canvas.width
    },

    //Otras funciones
    getMousePos(canvasDom, mouseEvent) {
      var rect = canvasDom.getBoundingClientRect()

      return {
        x: mouseEvent.clientX - rect.left,
        y: mouseEvent.clientY - rect.top,
      }
    },

    getTouchPos(canvasDom, touchEvent) {
      var rect = canvasDom.getBoundingClientRect()

      return {
        x: touchEvent.touches[0].clientX - rect.left,
        y: touchEvent.touches[0].clientY - rect.top,
      }
    },

    startTouch(event) {
      this.mousePos = this.getTouchPos(this.canvas, event)
      event.preventDefault()
      var touch = event.touches[0]
      var mouseEvent = new MouseEvent('mousedown', {
        clientX: touch.clientX,
        clientY: touch.clientY,
      })
      this.canvas.dispatchEvent(mouseEvent)
    },

    finishedTouch(event) {
      event.preventDefault()
      var mouseEvent = new MouseEvent('mouseup', {})
      this.canvas.dispatchEvent(mouseEvent)
    },

    leaveTouch(event) {
      event.preventDefault() // Prevent scrolling when touching the canvas
      var mouseEvent = new MouseEvent('mouseup', {})
      this.canvas.dispatchEvent(mouseEvent)
    },

    moveTouch(event) {
      event.preventDefault() // Prevent scrolling when touching the canvas
      var touch = event.touches[0]
      var mouseEvent = new MouseEvent('mousemove', {
        clientX: touch.clientX,
        clientY: touch.clientY,
      })
      this.canvas.dispatchEvent(mouseEvent)
    },
  },
}
</script>
