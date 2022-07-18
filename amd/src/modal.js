import $ from "jquery";
import "jqueryui";
import ModalFactory from "core/modal_factory";

export const initialise = () => {

  $(document).on('click', '#modal_card', (event) => {

      let trigger = $('#login');

      const name = event.target.getAttribute('data-name');
      const message = event.target.getAttribute('data-message');
      const date = event.target.getAttribute('data-date');

      // eslint-disable-next-line promise/catch-or-return
      ModalFactory.create({
        title: name,
        body: message,
        footer: date,
      }, trigger)
      // eslint-disable-next-line promise/always-return
      .then((modal) => {
        modal.show();
      });
  });
};