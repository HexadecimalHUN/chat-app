// resources/assets/js/components/ChatUsers.vue

<template>
  <ul class="list-unstyled chat-list mt-2 mb-0">
    <!-- <li class="clearfix active">
      <img
        src="https://bootdey.com/img/Content/avatar/avatar2.png"
        alt="avatar"
      />
      <div class="about">
        <div class="name">Aiden Chavez</div>
        <div class="status"><i class="fa fa-circle online"></i> online</div>
      </div>
    </li> -->

    <li
      class="clearfix"
      v-for="chatUser in chatUsers"
      :key="chatUser.id"
      @click="selectUser(chatUser)"
      :class="{ active: currentChatUserId === chatUser.id }"
    >
      <img
        src="https://bootdey.com/img/Content/avatar/avatar2.png"
        alt="avatar"
      />

      <!-- TODO:INSERT USER PROFILE PICTURES -->
      <!-- <img
        :src="chatUser.avatar_url"
        alt="avatar"
      /> -->

      <div class="about">
        <div class="name">{{ chatUser.name }}</div>
        <div class="status">
          <i class="fa fa-circle" :class="userIsOnline"></i> online
        </div>
      </div>
    </li>
  </ul>
</template>

<script>
export default {
  props: ["user"],

  data() {
    return {
      chatUsers: [],
      currentChatUser: null
    };
  },
  created() {
    this.getUsers();
  },

  computed: {
    // Need to add logic to this
    userIsOnline() {
      return "online";
    },
    currentChatUserId() {
      if (!this.currentChatUser) {
        return false;
      } else {
        return this.currentChatUser.id;
      }
    }
  },

  methods: {
    selectUser(userData) {
      this.currentChatUser = userData;
      this.$emit("selectuser", this.currentChatUser);
    },

    async getUsers() {
      // get all of the users from the database
      await axios
        .get("/chat/users")
        .then((response) => {
          this.chatUsers = response.data;
        })
        .catch((err) => {
          console.log(err);
        });
    }
  }
};
</script>