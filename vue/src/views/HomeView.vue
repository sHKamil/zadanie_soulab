<script setup lang="ts">
import axios from 'axios';
import { computed, reactive, ref, watchEffect } from 'vue'
import MessagesTable from '../components/MessagesTable.vue'
import { useMouse, useElementBounding } from '@vueuse/core'

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
const messages_data = ref<MessageType[]>([]);
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

// If ValidateForm returns true send form data to backend entrypoint
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

// Validate form after click and set value of inputs to empty string if they are undefined
const validateForm = (): boolean => {
  if(name_error.value.is_valid === true && last_name_error.value.is_valid === true && email_error.value.is_valid === true && message_error.value.is_valid === true) return true;
  if(name_error.value.is_valid === "NotValidatedYet") name.value = '';
  if(last_name_error.value.is_valid === "NotValidatedYet") last_name.value = '';
  if(email_error.value.is_valid === "NotValidatedYet") email.value = '';
  if(message_error.value.is_valid === "NotValidatedYet") message.value = '';
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

// Set inputs to initial value
const clearFields = () => {
  name.value = undefined;
  last_name.value = undefined;
  email.value = undefined;
  message.value = undefined;
}

// Get data for table from backend endpoint
const getMessagesForTable = async () => {
  let messages: MessageType[] = [];

  const data = await axios.get(backend_domain + '/api/message');
        data.data.forEach((message: MessageType) => {
          messages.push(message);
        });

  messages_data.value = messages;
}
getMessagesForTable();

// Styling form glow
const glow_form = ref(null);
const rect = reactive(useElementBounding(glow_form));
const { x, y } = useMouse();
const mouse_x = ref('');
const mouse_y = ref('');
watchEffect(() =>{mouse_x.value = x.value - rect.left- window.scrollX + "px"});
watchEffect(() =>{mouse_y.value = y.value - rect.top - window.scrollY + "px"});
</script>

<template>

  <div class="container">
    <form ref="glow_form" @submit.prevent="addMessage">
      <div class="inner-content">
        <ul>
          <li><input :class="{'wrong-input': name_error.is_valid === false}" type="text" v-model="name" placeholder="Imię"  /></li>
          <li><span class="error-message">{{ name_error.message }}</span></li>
          <li><input :class="{'wrong-input': last_name_error.is_valid === false}" type="text" v-model="last_name" placeholder="Nazwisko"  /></li>
          <li><span class="error-message">{{ last_name_error.message }}</span></li>
          <li><input :class="{'wrong-input': email_error.is_valid === false}" type="text" v-model="email" placeholder="E-mail"  /></li>
          <li><span class="error-message">{{ email_error.message }}</span></li>
          <li><textarea :class="{'wrong-input': message_error.is_valid === false}" v-model="message" placeholder="Wiadomość"></textarea></li>
          <li><span class="error-message">{{ message_error.message }}</span></li>
          <input type="submit" value="Dodaj">
        </ul>
      </div>
    </form>
      <MessagesTable v-if="messages_data.length>0" @messageDeleted="getMessagesForTable" :msg="messages_data" :backend_domain="backend_domain"/>
  </div>
</template>
<style scoped>
.container {
  width: 100%;
}
form {
  background-color: var(--vt-c-dark-soft);
  border-radius: 1rem;
  width: 40%;
  margin: 5rem auto;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: fit-content;
  background: radial-gradient(
    800px circle at v-bind(mouse_x) v-bind(mouse_y), 
    var(--vt-c-dark-light),
    transparent 60%
  );
}
form > .inner-content {
  border-radius: 1rem;
  width: calc(100% - 2px);
  height: calc(100% - 2px);
  margin: 1px;
  border: 1px solid var(--vt-c-dark-soft-transparent);
  background-color: var(--vt-c-dark-transparent);
}
ul {
  list-style: none;
  margin: 0 auto;
  padding: 2.5rem 0rem;
  width: fit-content;
}
ul>li:first-child>input{
  border-top-right-radius: .5rem;
  border-top-left-radius: .5rem;
}
li {
  width: 100%;
  min-height: 1.5rem;
}
input {
  background-color: var(--vt-c-dark);
  border: 1px solid var(--vt-c-dark-soft);
  color: white;
  padding: .8rem;
  font-size: 1.3rem;
  width: 100%;
  min-width: 15rem;
  outline: none;
  transition: all .2s ease;
}
input:focus {
  border-radius: .5rem;
  border: 1px solid var(--vt-c-dark-light-transparent);
}
textarea {
  background-color: var(--vt-c-dark);
  border: 1px solid var(--vt-c-dark-soft);
  padding: .8rem;
  color: white;
  width: 100%;
  min-width: 15rem;
  min-height: 7rem;
  outline: none;
  resize: none;
  transition: all .2s ease;
}
textarea:focus {
  border-radius: .5rem;
  border: 1px solid var(--vt-c-dark-light-transparent);
}
input[type="submit"]{
  background-color: var(--vt-c-dark-light-transparent);
  color: var(--vt-c-dark);
  margin-top: .5rem;
  cursor: pointer;
  transition: all .3s ease;
  font-weight: bold;
  border-bottom-left-radius: .5rem;
  border-bottom-right-radius: .5rem;
}
input[type="submit"]:hover{
  background-color: var(--vt-c-dark-light);
}
.wrong-input {
  border:1px solid var(--vt-c-dark-danger);
}
.error-message {
  color: var(--vt-c-dark-danger-soft);
}
</style>
