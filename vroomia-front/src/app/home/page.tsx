import React from "react";
import ChatComponent from "../components/chat.component";
import StepperComponent from "../components/stepper.component";
import Navbar from "../components/navbar.component";

const Home = () => {
  return (
    <div className="flex flex-column w-screen ">
      <div className="w-1/2 h-screen border-solid border-1-[#A9A9A9] box-border">
        <ChatComponent />
      </div>
      <div className="w-1/2 border-solid border-1-[#A9A9A9] box-border">
        <StepperComponent />
      </div>
      <div className="w-15">
        <Navbar/>
      </div>
    </div>
  );
};

export default Home;
