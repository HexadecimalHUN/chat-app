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
                      {{ calculateLastSeenTime(getSelectedUser.last_seen) }}
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
              :sessionId="sessionId"
            ></chat-form>
          </div>

          <div
            class="d-flex align-self-center w-100 justify-content-center"
            v-else
          >
            <h2>No chat open!</h2>
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
      sessionId: null
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
    sessionId(val, oldVal) {
      if (oldVal) {
        this.disconnectFromChat(oldVal);
        this.startChannel(val);
      }
    }
  },
  methods: {
    // TODO: FORMAT LAST SEEN TIME
    calculateLastSeenTime(date) {
      console.log(this.moment(Date.now()).toISOString());
      console.log(this.moment(date).toISOString());
      const nowDate = this.moment(Date.now());
      return this.moment.duration(nowDate.diff(date)).asSeconds();
    },

    startChannel(sessionId) {
      window.Echo.private(`private-chat.${sessionId}`).listen(
        "MessageSent",
        (e) => {
          console.log(e);
          this.messages.push(e.message);
        }
      );
    },
    disconnectFromChat(sessionId) {
      window.Echo.leave(`private-chat.${sessionId}`);
    },

    //TODO: FIX TYPING INDICATOR
    lisenForWhisper(sessionId) {
      Echo.private(`private-chat.${sessionId}`).listenForWhisper(
        "typing",
        (e) => {
          console.log("typing..");
          this.isTyping = true;
          setTimeout(() => {
            this.isTyping = false;
          }, 2000);
        }
      );
    },
    async selectCurrUser(userData) {
      this.selectedUser = userData;
      this.friendId = userData.id;

      await this.fetchSession();
      this.fetchMessages();
      this.lisenForWhisper(this.sessionId);
      this.startChannel(this.sessionId);
    },

    async fetchSession() {
      await axios.get(`chat/session/${this.friendId}`).then((response) => {
        this.sessionId = response.data;
      });
    },

    fetchMessages() {
      axios
        .get(`chat/${this.sessionId}/messages`)
        .then((response) => (this.messages = response.data));
    },

    addMessage(message) {
      this.messages.push(message);
      axios
        .post(`chat/${this.sessionId}/messages`, message)
        .then((response) => console.log(response.data));
    }
  }
};
</script>
