<script setup lang="ts">
import axios from "axios";

const props = defineProps({
    msg: Object,
    backend_domain: String
});

const emit = defineEmits(['messageDeleted']);

const deleteMessage = async (message_id: number) => {
  const response = await axios.delete(props.backend_domain + `/api/message/${message_id}`);
  if(response.status === 200) {
    console.log(response.data.message);
    emit('messageDeleted')
  }
}


</script>

<template>
    <table>
      <tr>
        <th>ID</th>
        <th>Imię</th>
        <th>Nazwisko</th>
        <th>Wiadomość</th>
      </tr>
      <tr v-for="(message, i) in msg" :key="i" :msg="message">
        <td>{{ message.id }}</td>
        <td>{{ message.name }}</td>
        <td>{{ message.last_name }}</td>
        <td>{{ message.email }}</td>
        <td>{{ message.message }}</td>
        <td><button @click="deleteMessage(message.id)">USUŃ</button></td>
      </tr>
    </table>
</template>

<style scoped>
</style>
