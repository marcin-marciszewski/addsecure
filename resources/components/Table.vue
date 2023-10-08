<template>
  <div>
    <v-data-table :headers="headers" :items="vehicles" sort-by="calories" class="elevation-1">
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Lista</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                New Item
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="4">
                      <v-text-field v-model="editedItem.registrationNumber" label="Nr rejestracyjny"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                      <v-text-field v-model="editedItem.brand" label="Marka"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                      <v-text-field v-model="editedItem.model" label="Model"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                      <v-text-field v-model="editedItem.type" label="Typ pojazdu"></v-text-field>
                    </v-col>

                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">
                  Cancel
                </v-btn>
                <v-btn color="blue darken-1" text @click="save">
                  Save
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)">
          mdi-pencil
        </v-icon>
        <v-icon small @click="deleteItem(item)">
          mdi-delete
        </v-icon>
      </template>
      <template v-slot:item.id="{ item }">
        {{ vehicles.indexOf(item) + 1 }}
      </template>
      <template v-slot:item.registrationNumber="{ item }">
        <span>{{ item.registrationNumber.toUpperCase() }}</span>
      </template>
      <template v-slot:item.updatedAt="{ item }">
        <span>{{ formatDateTime(item.updatedAt) }}</span>
      </template>
      <template v-slot:item.createdAt="{ item }">
        <span>{{ formatDateTime(item.createdAt) }}</span>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data: () => ({
    baseUrl: 'http://localhost:8008',
    vehicles: [],
    dialog: false,
    dialogDelete: false,
    headers: [
      {
        text: 'Lp.',
        align: 'start',
        value: 'id',
      },
      { text: 'Nr rejestracyjny', value: 'registrationNumber' },
      { text: 'Marka', value: 'brand' },
      { text: 'Model', value: 'model' },
      { text: 'Typ pojazdu', value: 'type' },
      { text: 'Data utworzenia', value: 'createdAt' },
      { text: 'Data modyfikacji', value: 'updatedAt' },
      { text: 'Akcje', value: 'actions', sortable: false },
    ],
    editedIndex: -1,
    editedItem: {
      id: null,
      registrationNumber: '',
      brand: '',
      model: '',
      type: '',
      updatedAt: 0,
      createdAt: 0,
    },
    defaultItem: {
      id: null,
      registrationNumber: '',
      brand: '',
      model: '',
      type: '',
      updatedAt: 0,
      createdAt: 0,
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
    },

  },

  watch: {
    dialog(val) {
      val || this.close()
    },
    dialogDelete(val) {
      val || this.closeDelete()
    },
  },

  created() {
    this.initialize()
  },

  methods: {
    initialize() {
      axios.get(`${this.baseUrl}/vehicles/list`)
        .then(response => {
          const vehicles = response.data.results
          this.vehicles = vehicles
        })
        .catch(e => {
          console.error(e)
        })
    },

    formatDateTime(date) {
      date = new Date(date)
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');

      return `${year}-${month}-${day} ${hours}:${minutes}`;
    },

    sendData(vehicle, newVehicle = false, deleteVehicle = false) {
      axios.post(`${this.baseUrl}/vehicles/${deleteVehicle ? 'delete' : 'save'}/${newVehicle ? 0 : vehicle.id}`, vehicle)
        .catch(function (error) {
          console.error(error);
        })
    },

    editItem(item) {
      this.editedIndex = this.vehicles.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.editedItem.updatedAt = +new Date()
      this.dialog = true
    },

    deleteItem(item) {
      this.editedIndex = this.vehicles.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.sendData(this.editedItem, false, true)
      this.dialogDelete = true
    },

    deleteItemConfirm() {
      this.vehicles.splice(this.editedIndex, 1)
      this.closeDelete()
    },

    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    closeDelete() {
      this.dialogDelete = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    save() {
      if (this.editedIndex > -1) {
        this.editedItem.updatedAt = +new Date()
        Object.assign(this.vehicles[this.editedIndex], this.editedItem)
        this.sendData(this.editedItem)
      } else {
        this.editedItem.id = 0
        this.editedItem.createdAt = +new Date()
        this.editedItem.updatedAt = +new Date()
        this.vehicles.push(this.editedItem)
        this.sendData(this.editedItem, true)
      }
      this.close()
    },
  },
}
</script>
