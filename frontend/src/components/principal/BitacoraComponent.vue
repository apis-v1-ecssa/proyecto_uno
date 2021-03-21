<template>
  <v-row justify="space-around">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12" v-if="!mostrar_bitacora">
      <v-data-table
        :headers="dessertHeaders"
        :items="desserts"
        :items-per-page.sync="itemsPerPage"
        item-key="docto_no"
        class="elevation-1"
        :search="search"
        :no-data-text="'No hay registro de bitácoras'"
        :no-results-text="'El registro que usted busca no se encuentra'"
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-toolbar-title
              >Bitácora de movimientos en las entregas</v-toolbar-title
            >
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
          <v-chip :color="item.color" dark>
            {{ item.name }}
          </v-chip>
        </template>
        <template v-slot:item.move="{ item }">
          <v-btn color="success" @click="bitacora(item)">
            <v-icon>mdi-history</v-icon>
            <span>Ver</span>
          </v-btn>
        </template>
      </v-data-table>
    </v-col>
    <v-col cols="12" md="12" v-else>
      <v-container>
        <v-card>
          <v-img
            height="200px"
            src="https://cdn.vuetifyjs.com/images/cards/server-room.jpg"
          >
            <v-app-bar flat color="rgba(0, 0, 0, 0)">
              <v-btn color="white" icon @click="bitacora(seleccionado)">
                <v-icon>mdi-history</v-icon>
              </v-btn>

              <v-toolbar-title class="title white--text pl-0">
                Documento No: {{ seleccionado.docto_no }} |
                {{ seleccionado.series }}
              </v-toolbar-title>

              <v-spacer></v-spacer>

              <v-btn color="white" icon @click="mostrar_bitacora = false">
                <v-icon>exit_to_app</v-icon>
              </v-btn>
            </v-app-bar>
          </v-img>

          <v-list class="transparent">
            <v-list-item>
              <v-list-item-title
                ><p class="ml-3">
                  Vendedor: {{ seleccionado.seller }}
                </p></v-list-item-title
              >
            </v-list-item>
            <v-list-item>
              <v-list-item-title
                ><p class="ml-3">
                  Cliente: {{ seleccionado.client }}
                </p></v-list-item-title
              >
            </v-list-item>
            <v-list-item>
              <v-list-item-title
                ><p class="ml-3">
                  Bodeguero: {{ seleccionado.usuario }}
                </p></v-list-item-title
              >
            </v-list-item>
          </v-list>

          <v-divider></v-divider>

          <v-card-text>
            <v-row align="center">
              <v-col class="display-1" cols="12"> Fecha de Ingreso </v-col>
              <v-col class="display-3" cols="12">
                {{ seleccionado.date }}
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <div class="font-weight-bold ml-8 mb-2">Movimientos</div>

                <v-timeline align-top dense>
                  <v-timeline-item
                    v-for="item in bitacoras"
                    :key="item.date"
                    :color="item.color"
                    large
                  >
                    <div>
                      <div class="font-weight-normal">
                        <strong>{{ item.name }}</strong> @{{ item.date }}
                      </div>
                      <div>{{ item.usuario }}</div>
                    </div>
                  </v-timeline-item>
                </v-timeline>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-container>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: "Anulado",
  data() {
    return {
      loading: false,
      mostrar_bitacora: false,
      search: "",
      itemsPerPage: 25,
      dessertHeaders: [
        {
          text: "Documento",
          align: "start",
          value: "docto_no",
        },
        { text: "Serie", value: "series" },
        { text: "Asignado", value: "usuario" },
        { text: "Vendedor", value: "seller" },
        { text: "Ingresado", value: "date" },
        { text: "Cliente", value: "client" },
        { text: "Estado", value: "status" },
        { text: "Movimientos", value: "move", sortable: false },
      ],
      desserts: [],
      bitacoras: [],
      seleccionado: {},
    };
  },

  created() {
    this.initialize();
  },

  methods: {
    initialize() {
      this.loading = true;
      this.mostrar_bitacora = false;
      this.seleccionado = {};
      this.bitacoras = [];

      this.$store.state.services.bitacoraService
        .index()
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
          this.loading = false;
        })
        .catch((r) => {
          this.loading = false;
        });
    },

    bitacora(item) {
      this.loading = true;
      this.mostrar_bitacora = true;
      this.seleccionado = item;

      this.$store.state.services.bitacoraService
        .venta(item)
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

          this.bitacoras = r.data;
          this.loading = false;
        })
        .catch((r) => {
          this.loading = false;
        });
    },
  },
};
</script>