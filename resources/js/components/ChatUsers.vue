// resources/assets/js/components/ChatUsers.vue

<template>
  <div>
    <ul class="list-unstyled chat-list mt-2 mb-0" v-if="!searchTerm">
      <li
        class="clearfix d-flex"
        v-for="chatUser in chatUsers"
        :key="chatUser.id"
        @click="selectUser(chatUser)"
        :class="{ active: currentChatUserId === chatUser.id }"
      >
        <!-- TODO:INSERT USER PROFILE PICTURES -->
        <img :src="chatUser.avatar_url" alt="avatar" />

        <div class="about">
          <div class="name">{{ chatUser.name }}</div>
          <div class="status">
            <i
              class="fa fa-circle"
              :class="chatUser.online ? 'online' : ''"
            ></i>
            {{
              chatUser.online
                ? "Online"
                : moment().from(chatUser.last_seen, true) + "ago."
            }}
          </div>
        </div>
      </li>
    </ul>

    <ul class="list-unstyled chat-list mt-2 mb-0" v-else>
      <li
        class="clearfix d-flex"
        v-for="chatUser in filteredUsers"
        :key="chatUser.id"
        @click="selectUser(chatUser)"
      >
        <img :src="chatUser.avatar_url" alt="avatar" />

        <div class="about">
          <div class="name">{{ chatUser.name }}</div>
          <div class="status">
            <i
              class="fa fa-circle"
              :class="chatUser.online ? 'online' : ''"
            ></i>
            {{
              chatUser.online
                ? "Online"
                : moment().from(chatUser.last_seen, true) + "ago."
            }}
          </div>
        </div>
      </li>
      <li class="text-center" v-if="!filteredUsers[0]" @click="clear">
        No user found! :(
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: ["user", "searchTerm"],

  data() {
    return {
      chatUsers: [],
      filteredUsers: [],
      currentChatUser: null
    };
  },
  watch: {
    searchTerm(val, oldVal) {
      const re = new RegExp(val, "gi");
      this.filteredUsers = this.chatUsers.filter((u) => u.name.match(re));
    }
  },

  async created() {
    await this.getUsers();
  },
  mounted() {
    this.presenceIndicator();
  },

  computed: {
    // Need to add logic to this

    currentChatUserId() {
      if (!this.currentChatUser) {
        return false;
      } else {
        return this.currentChatUser.id;
      }
    }
  },

  methods: {
    presenceIndicator() {
      Echo.join(`chat`)
        .here((users) => {
          this.chatUsers.forEach((friend, i) => {
            users.forEach((user) => {
              if (user.id == friend.id) {
                this.$set(friend, "online", true);
              }
            });
          });
        })
        .joining((user) => {
          const logedInUse = this.chatUsers.find((u) => u.id == user.id);
          this.$set(logedInUse, "online", true);
        })
        .leaving((user) => {
          const logedInUse = this.chatUsers.find((u) => u.id == user.id);
          this.$set(logedInUse, "online", false);
          // TODO: store leave time
        })
        .error((error) => {
          console.error(error);
        });
    },
    selectUser(userData) {
      this.currentChatUser = userData;
      this.searchTerm = "";
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
      // this.chatUsers.forEach((u) => {
      //   await axios
      //     .get("/chat/${chatroomId}/unseen-messages/{userId}")
      //     .then((response) => {
      //       this.chatUsers = response.data;
      //     })
      //     .catch((err) => {
      //       console.log(err);
      //     });
      // });
    }
  }
};
</script>