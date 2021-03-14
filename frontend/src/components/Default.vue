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
      >
        <template v-slot:header>
          <v-toolbar class="mb-2" color="indigo darken-5" dark flat>
            <v-toolbar-title> Ordenes facturadas </v-toolbar-title>
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
            <v-btn class="mr-1" color="success" @click="facturados">Recargar</v-btn>
          </v-toolbar>
        </template>

        <template v-slot:default="props">
          <v-row>
            <v-col
              v-for="item in props.items"
              :key="item.docto_no"
              cols="12"
              md="6"
            >
              <v-card class="mx-auto" :color="item.status.color" dark>
                <v-card-title class="subheading font-weight-bold">
                  Documento No. {{ item.docto_no }} | Vendedor {{ item.seller }}
                </v-card-title>

                <v-divider></v-divider>

                <v-list
                  transparent
                  v-for="producto in item.detail"
                  v-bind:key="producto.id"
                >
                  <v-list-item>
                    <v-list-item-content>
                      <v-list-item-title
                        ><h3>{{ producto.item_code }}</h3></v-list-item-title
                      >
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

                <v-card-actions>
                  <v-btn class="mr-1" color="primary" @click="guardar(item)">
                    Guardar avance
                  </v-btn>
                  <v-btn class="mr-1" color="success" @click="entregar(item)">
                    Entrega completo
                  </v-btn>
                  <v-btn class="mr-1" color="error" @click="cancelar(item)">
                    Cancelar
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </template>
      </v-data-iterator>
    </v-container>
  </v-row>
</template>

<script>
export default {
  name: "Default",
  components: {},
  data() {
    return {
      loading: false,
      itemsPerPage: 100,
      search: "",
      items: [],
    };
  },

  created() {
    this.facturados();
  },

  methods: {
    facturados() {
      this.loading = true;

      this.$store.state.services.ventaService
        .facturado()
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

          console.log(r.data);
          this.items = r.data;
          this.loading = false;
        })
        .catch((r) => {
          this.loading = false;
        });
    },

    entregar(item) {
      this.$swal({
        title: "Entregar Producto Completo",
        text: "¿Está seguro de realizar esta acción?",
        type: "success",
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          let data = new Object();
          data.id = item.id;

          this.loading = true;
          this.$store.state.services.ventaService
            .completar_todo(data)
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
              this.facturados();
            })
            .catch((r) => {
              this.loading = false;
            });
        } else {
          this.close();
        }
      });
    },

    guardar(item) {
      this.$swal({
        title: "Entregar Producto",
        text: "¿Está seguro de realizar esta acción?",
        type: "success",
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          let data = new Object();
          data.id = item.id;
          data.detail = item.detail;

          this.loading = true;
          this.$store.state.services.ventaService
            .entregar(data)
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
              this.facturados();
            })
            .catch((r) => {
              this.loading = false;
            });
        } else {
          this.close();
        }
      });
    },

    cancelar(item) {
      this.$swal({
        title: "Cancelar Entrega",
        text: "¿Está seguro de realizar esta acción?",
        type: "error",
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          let data = new Object();
          data.id = item.id;

          this.loading = true;
          this.$store.state.services.ventaService
            .cancelar(data)
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
              this.facturados();
            })
            .catch((r) => {
              this.loading = false;
            });
        } else {
          this.close();
        }
      });
    },
  },
};
</script>
