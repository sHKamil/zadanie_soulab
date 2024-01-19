<script setup lang="ts">
import axios from 'axios';
import { computed, ref} from 'vue'
import MessagesTable from '../components/MessagesTable.vue'

type MessageType = {
    id: number,
    name: string,
    last_name: string,
    email: string,
    message: string,
}

type ValidatorResult = {
  is_valid: boolean | "NotValidatedYet",
  message?: string
}

const backend_domain: string = 'http://localhost:8070';
const name = ref();
const last_name = ref();
const email = ref();
const message = ref();

const name_error = computed(() => {
  return validateTextInput(name.value);
});
const last_name_error = computed(() => {
  return validateTextInput(last_name.value)
});
const email_error = computed(() => {
  return validateEmail(email.value);
});
const message_error = computed(() => {
  return validateTextInput(message.value);
});

const addMessage = async () => {
  if(validateForm()) {
    axios.post(backend_domain + '/api/message', {
      name: name.value,
      last_name: last_name.value,
      email: email.value,
      message: message.value
    }).then(response=> {      
        if(response.status === 200) {
          clearFields();
          getMessagesForTable();
        }
    }).catch(error => {
        getMessagesForTable();
        console.error(error.response.data)
    });
  }
}

const validateForm = (): boolean => {
  if(name_error.value.is_valid === true && last_name_error.value.is_valid === true && email_error.value.is_valid === true && message_error.value.is_valid === true) return true;
  return false;
}

const validateEmail = (email: string) => {
  const regex: RegExp = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;

  if(email === undefined) return {is_valid: "NotValidatedYet"} as ValidatorResult;
  if(!email) return {is_valid: false, message: 'To pole jest wymagane'} as ValidatorResult;
  if (!regex.test(email)) {
    return {is_valid: false, message: 'Adres e-mail musi być prawidłowy'} as ValidatorResult;
  }
  if (email.length > 250) {
    return {is_valid: false, message: 'Za dużo znaków (Max 250)'} as ValidatorResult;
  }
  return {is_valid: true} as ValidatorResult;
}

const validateTextInput = (text: string) => {
  if(text === undefined) return {is_valid: "NotValidatedYet"} as ValidatorResult;
  if(!text) return {is_valid: false, message: 'To pole jest wymagane'} as ValidatorResult;
  if (text.length > 250) {
    return {is_valid: false, message: 'Za dużo znaków (Max 250)'} as ValidatorResult;
  }
  return {is_valid: true} as ValidatorResult;
}

const clearFields = () => {
  name.value = undefined;
  last_name.value = undefined;
  email.value = undefined;
  message.value = undefined;
}

const data_ready = ref(false);
const messages_data = ref<MessageType[]>([]);

const getMessagesForTable = async () => {
  let messages: MessageType[] = [];

  const data = await axios.get(backend_domain + '/api/message');
        data.data.forEach((message: MessageType) => {
          messages.push(message);
        });

  messages_data.value = messages;
  data_ready.value = true;
}
getMessagesForTable();

</script>

<template>
  <div class="container">
    <form @submit.prevent="addMessage">
      <ul>
        <li><input type="text" v-model="name" placeholder="Imię"  /></li>
        <li><span class="error-message">{{ name_error.message }}</span></li>
      </ul>
      <ul>
        <li><input type="text" v-model="last_name" placeholder="Nazwisko"  /></li>
        <li><span class="error-message">{{ last_name_error.message }}</span></li>
      </ul>
      <ul>
        <li><input type="text" v-model="email" placeholder="E-mail"  /></li>
        <li><span class="error-message">{{ email_error.message }}</span></li>
      </ul>
      <ul>
        <li><textarea v-model="message" placeholder="Wiadomość"></textarea></li>
        <li><span class="error-message">{{ message_error.message }}</span></li>
      </ul>
      <ul>
        <input type="submit" value="Dodaj">
      </ul>
    </form>
      <MessagesTable @messageDeleted="getMessagesForTable" :msg="messages_data" :backend_domain="backend_domain"/>
  </div>
</template>
<style scoped>
.container {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
form {
  display: flex;
  flex-direction: column;
  gap: .5rem;
  padding: 3.5rem;
  background-color: rgb(17, 17, 17);
  box-shadow: 0px 0px 45px -16px rgb(117, 134, 116);
}
input {
  padding: .8rem;
  font-size: 1.3rem;
  outline: none;
  border: none;
  transition: all .2s ease;
}
input:focus {
  border-radius: .5rem;
}
input[type="submit"]{
  cursor: pointer;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  min-height: 1.5rem;
}
</style>