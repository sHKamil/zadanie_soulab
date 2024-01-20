<script setup lang="ts">
import axios from "axios";
import { useMouse, useElementBounding } from '@vueuse/core'
import { reactive, ref, watch } from 'vue'

const props = defineProps({
    msg: Object,
    backend_domain: String
});

const emit = defineEmits(['messageDeleted']);

const glow_messages = ref(null);
const { x, y } = useMouse();
const mouse_x = ref('');
const mouse_y = ref('');
const rect = reactive(useElementBounding(glow_messages));
watch(x, () =>{mouse_x.value = x.value - rect.left + "px"});
watch(y, () =>{mouse_y.value = y.value - rect.top - window.scrollY + "px"});

const deleteMessage = async (message_id: number) => {
  const response = await axios.delete(props.backend_domain + `/api/message/${message_id}`);
  if(response.status === 200) {
    emit('messageDeleted')
  }
}

</script>

<template>
  <div ref="glow_messages" class="messages">
    <div class="messages-inner">
      <span>Historia</span>
      <table>
        <tr>
          <th>ID</th>
          <th>Imię</th>
          <th>Nazwisko</th>
          <th>E-mail</th>
          <th>Wiadomość</th>
          <th></th>
        </tr>
        <tbody>
          <tr v-for="(message, i) in msg" :key="i" :msg="message">
            <td>{{ message.id }}</td>
            <td>{{ message.name }}</td>
            <td>{{ message.last_name }}</td>
            <td>{{ message.email }}</td>
            <td class="message-row">{{ message.message }}</td>
            <td><button @click="deleteMessage(message.id)">USUŃ</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.messages {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  min-width: 40rem;
  margin: 0 auto;
  border: 1px solid var(--vt-c-dark-soft);
  border-radius: 1rem;
  background: radial-gradient(
    800px circle at v-bind(mouse_x) v-bind(mouse_y), 
    var(--vt-c-dark-light),
    transparent 60%
  );
}
.messages-inner {
  margin: 1px;
  width: 100%;
  background-color: var(--vt-c-dark);
  border-radius: 1rem;
  padding: 2.5rem;
  width: calc(100% - 2px);
  height: calc(100% - 2px);
}
span {
 font-size: 3rem;
}
table {
  border-collapse: collapse;
  border-radius: 1rem;
  border-style: hidden;
  box-shadow: 0 0 0 1px var(--vt-c-dark-soft); 
  width: 100%;
  table-layout:fixed;
}
td {
  padding: 1rem;
  text-align: center;
  border-top: 1px solid var(--vt-c-dark-soft);
  word-wrap: break-word;
}
.message-row {
  text-align: left;
}
button {
  padding: .7rem;
  background-color: var(--vt-c-dark-danger);
  border: none;
  cursor: pointer;
  color: var(--vt-c-dark);
  font-weight: bold;
  font-size: 1rem;
  transition: all .3s ease;
}
button:hover {
  background-color: var(--vt-c-dark-danger-soft);
}
</style>
