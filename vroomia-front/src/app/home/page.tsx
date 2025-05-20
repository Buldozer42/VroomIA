import React from "react";
import ChatComponent from "../components/chat.component";
import StepperComponent from "../components/stepper.component";
import Navbar from "../components/navbar.component";

const Home = () => {
  return (
    <div className="flex flex-column w-screen overflow-y-hidden sm:overflow-y-auto">
      <div className="w-full lg:w-1/2 h-screen box-border">
        <ChatComponent />
      </div>
      <div className="w-1/2 border-solid border-1-[#A9A9A9] box-border hidden lg:block">
        <StepperComponent />
      </div>
      <div className="w-15 box-border">
        <Navbar />
      </div>
    </div>
  );
};

export default Home;
