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
        :no-data-text="'No hay facturaciones anuladas'"
        :no-results-text="'El registro que usted busca no se encuentra'"
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-toolbar-title>Entregas anuladas</v-toolbar-title>
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
          <v-chip :color="item.status.color" dark>
            {{ item.status.name }}
          </v-chip>
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
  </v-row>
</template>

<script>
export default {
  name: "Anulado",
  data() {
    return {
      loading: false,
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
        { text: "Anulado", value: "delivery_time" },
        { text: "Detalle", value: "data-table-expand", sortable: false },
        { text: "Estado", value: "status", sortable: false },
      ],
      desserts: [],
    };
  },

  created() {
    this.initialize();
  },

  methods: {
    initialize() {
      this.loading = true;

      this.$store.state.services.ventaService
        .anulado()
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
  },
};
</script>