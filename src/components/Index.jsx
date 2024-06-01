// import React from "react";
// import { UserProvider, useUser } from "./UserContext";
// import IndexOrganizer from "./IndexOrganizer";
// import IndexAdmin from "./IndexAdmin";
// import IndexUser from "./IndexUser";

// function Index() {
//   const { user } = useUser();

//   const renderContent = () => {
//     if (!user) return <IndexUser />;
//     switch (user.role) {
//       case "organizer":
//         return <IndexOrganizer />;
//       case "admin":
//         return <IndexAdmin />;
//       default:
//         return <IndexUser />;
//     }
//   };

//   return <>{renderContent()}</>;
// }

// export default Index;
