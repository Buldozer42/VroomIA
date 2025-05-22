// components/NotificationDot.tsx
const NotificationDot = () => (
    <div className="relative w-3 h-3 ml-2">
      <div className="absolute inline-flex h-full w-full rounded-full bg-error opacity-75 animate-ping"></div>
      <div className="relative inline-flex rounded-full h-3 w-3 bg-error border-2 border-base-100"></div>
    </div>
  );
  
  export default NotificationDot;