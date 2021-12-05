// resources/assets/js/components/ChatBody.vue

<template>
  <div class="container">
    <div class="row clearfix">
      <div class="col-lg-12">
        <div class="card chat-app d-flex flex-row">
          <div id="plist" class="people-list col-3">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"
                  ><i class="fa fa-search"></i
                ></span>
              </div>
              <input type="text" class="form-control" placeholder="Search..." />
            </div>

            <chat-users v-on:selectuser="selectCurrUser"></chat-users>
          </div>

          <div class="chat col-9 d-flex flex-column" v-if="!!selectedUser">
            <div class="chat-header clearfix">
              <div class="row">
                <div class="col-lg-6">
                  <a
                    href="javascript:void(0);"
                    data-toggle="modal"
                    data-target="#view_info"
                  >
                    <img
                      src="https://bootdey.com/img/Content/avatar/avatar2.png"
                      alt="avatar"
                    />
                    <!-- <img
                      :src="getSelectedUser.avatar_url"
                      alt="avatar"
                    /> -->
                  </a>

                  <div class="chat-about">
                    <h6 class="m-b-0">{{ getSelectedUser.name }}</h6>
                    <small
                      >Last seen:
                      {{ moment(Date.now()).from(getSelectedUser.last_seen) }}
                    </small>
                  </div>
                </div>
              </div>
            </div>

            <!-- TODO: Change parner link -->
            <chat-messages
              :messages="messages"
              :isTyping="isTyping"
              :user="user"
            ></chat-messages>


            <chat-form
              v-on:messagesent="addMessage"
              :user="user"
              :roomId="roomId"
            ></chat-form>
          </div>

          <div
            class="d-flex align-self-center w-100 justify-content-center"
            v-else
          >
            <h2>No chat opened yet!</h2>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["user"],

  data() {
    return {
      friendId: null,
      messages: [],
      isTyping: false,
      selectedUser: null,
      roomId: null
    };
  },

  computed: {
    getSelectedUser() {
      console.log(this.selectedUser);
      if (!this.selectedUser) {
        return {
          name: "",
          last_seen: ""
        };
      } else {
        return this.selectedUser;
      }
    }
  },

  watch: {
    roomId(val, oldVal) {
      if (oldVal) {
        this.disconnectFromChat(oldVal);
        this.connectToChat(val);
      }
    }
  },
  methods: {
    calculateLastSeenTime(date) {
      const nowDate = this.moment(Date.now());
      return this.moment.duration(nowDate.diff(date)).asSeconds();
    },

    connectToChat(roomId) {
      const channel = window.Echo.private(`chat.${roomId}`);

      channel
        .listen("MessageSent", (e) => {
          console.log(e);
          this.messages.push(e.message);
        })
        .listenForWhisper("typing", (e) => {
          this.isTyping = true;
          setTimeout(() => {
            this.isTyping = false;
          }, 3000);
        });
    },
    disconnectFromChat(roomId) {
      window.Echo.leave(`chat.${roomId}`);
    },

    async selectCurrUser(userData) {
      this.selectedUser = userData;
      this.friendId = userData.id;

      await this.fetchRoomId();
      this.fetchMessages(this.roomId);

      this.connectToChat(this.roomId);
    },

    async fetchRoomId() {
      await axios.get(`chat/session/${this.friendId}`).then((response) => {
        this.roomId = response.data;
      });
    },

    fetchMessages(roomId) {
      axios
        .get(`chat/${roomId}/messages`)
        .then((response) => (this.messages = response.data));
    },

    addMessage(message) {
      this.messages.push(message);
      axios
        .post(`chat/${this.roomId}/messages`, message)
        .then((response) => console.log(response.data));
    }
  }
};
</script>
