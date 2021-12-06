// resources/assets/js/components/MessageActions.vue

<template>
  <div class="action-box align-self-center px-3" v-if="!message.is_removed">
    <i
      v-if="message.user_id == user.id"
      class="fas fa-trash-alt m-1"
      @click.prevent="deleteMessage(message.id)"
    ></i>
    <i class="fas fa-thumbtack m-1" @click.prevent="pinMessage(message)"></i>
  </div>
</template>

<script>
export default {
  props: ["message", "user"],
  methods: {
    deleteMessage(messageId) {
      this.$set(this.message, "is_removed", true);

      axios
        .put(`chat/${this.message.chat_room_id}/remove-message/${messageId}`)
        .then((response) => console.log(response.data));
    },
    pinMessage(message) {
      this.$emit("pinMessage", message);
      axios
        .post(`chat/${this.message.chat_room_id}/pin-message`, message)
        .then((response) => console.log(response.data));
    }
  }
};
</script>