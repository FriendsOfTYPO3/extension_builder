console.log('Test');
import Notification from '@typo3/backend/notification.js';

function notImplemented() {
  Notification.notice(
    'Notice',
    'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt'
  );
};

export { notImplemented };
